<?php

namespace App\Actions\Nodes;

use App\Actions\Services\StartDeployment;
use App\Models\DeploymentData;
use App\Models\DeploymentData\LaunchMode;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTasks\UpdateCurrentNode\UpdateCurrentNodeMeta;
use App\Models\NodeTaskType;
use App\Models\Swarm;
use App\Models\SwarmData;
use App\Models\User;
use App\Util\ResourceId;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class InitCluster
{
    use AsAction;

    public function rules(): array
    {
        return [
            'node_id' => ['required', 'exists:nodes,id'],
            'advertise_addr' => ['required', 'ipv4'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $node = Node::with('team')->findOrFail($request->node_id);

        return $request->user()->belongsToTeam($node->team);
    }

    public function handle(User $user, Node $node, string $advertiseAddr): Swarm
    {
        return DB::transaction(function () use ($node, $advertiseAddr, $user) {
            $teamId = $user->currentTeam->id;

            $swarm = $this->createSwarm($teamId);

            $node->swarm_id = $swarm->id;
            $node->saveQuietly();

            $network = $this->createNetwork($swarm->id, $teamId);
            $taskGroup = $this->createTaskGroup($swarm->id, $node->id, $user->id, $teamId);

            $tasks = $this->createTasks($swarm, $node, $network, $advertiseAddr);

            $taskGroup->tasks()->createMany($tasks);

            $this->createCaddyService($swarm, $network, $node, $teamId);

            return $swarm;
        });
    }

    public function asController(ActionRequest $request)
    {
        $node = Node::findOrFail($request->get('node_id'));

        $this->handle($request->user(), $node, $request->get('advertise_addr'), $request->get('name'));
    }

    private function createSwarm(int $teamId): Swarm
    {
        return Swarm::create([
            'team_id' => $teamId,
            'data' => SwarmData::validateAndCreate([
                'registriesRev' => 0,
                'registries' => [],
                's3StoragesRev' => 0,
                's3Storages' => [],
                'joinTokens' => [
                    'worker' => '-',
                    'manager' => '-',
                ],
                'managerNodes' => [],
                'encryptionKey' => '-',
                'publicSSHKey' => '-',
            ]),
        ]);
    }

    private function createNetwork(int $swarmId, int $teamId): Network
    {
        return Network::create([
            'swarm_id' => $swarmId,
            'team_id' => $teamId,
            'name' => dockerize_name('ptah-net'),
        ]);
    }

    private function createTaskGroup(int $swarmId, int $nodeId, int $userId, int $teamId): NodeTaskGroup
    {
        return NodeTaskGroup::create([
            'type' => NodeTaskGroupType::InitSwarm,
            'swarm_id' => $swarmId,
            'node_id' => $nodeId,
            'invoker_id' => $userId,
            'team_id' => $teamId,
        ]);
    }

    private function createTasks(Swarm $swarm, Node $node, Network $network, string $advertiseAddr): array
    {
        return [
            [
                'type' => NodeTaskType::InitSwarm,
                'meta' => InitSwarmMeta::from([
                    'swarmId' => $swarm->id,
                    'forceNewCluster' => false,
                ]),
                'payload' => [
                    'SwarmInitRequest' => [
                        'ForceNewCluster' => false,
                        'ListenAddr' => '0.0.0.0:2377',
                        'AdvertiseAddr' => $advertiseAddr,
                        'Spec' => [
                            'Annotations' => [
                                'Name' => 'default',
                                'Labels' => dockerize_labels([
                                    'swarm.id' => $swarm->id,
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => NodeTaskType::UpdateCurrentNode,
                'meta' => UpdateCurrentNodeMeta::from([
                    'nodeId' => $node->id,
                ]),
                'payload' => [
                    'NodeSpec' => [
                        'Name' => $node->name,
                        'Availability' => 'active',
                        'Role' => 'manager',
                        'Labels' => dockerize_labels([
                            'node.id' => $node->id,
                            'node.name' => $node->name,
                        ]),
                    ],
                ],
            ],
            [
                'type' => NodeTaskType::CreateNetwork,
                'meta' => CreateNetworkMeta::from(['networkId' => $network->id, 'name' => $network->name]),
                'payload' => [
                    'NetworkName' => $network->docker_name,
                    'NetworkCreateOptions' => [
                        'Driver' => 'overlay',
                        'Labels' => dockerize_labels([
                            'network.id' => $network->id,
                        ]),
                        'Scope' => 'swarm',
                        'Attachable' => true,
                    ],
                ],
            ],
        ];
    }

    private function createCaddyService(Swarm $swarm, Network $network, Node $node, int $teamId): void
    {
        $caddyService = $swarm->services()->create([
            'name' => 'ptah',
            'team_id' => $teamId,
        ]);

        $deploymentData = $this->createCaddyDeploymentData($network, $node);

        $taskGroup = NodeTaskGroup::createForUser($caddyService->team->owner, $caddyService->team, NodeTaskGroupType::LaunchService);

        StartDeployment::run($taskGroup, $caddyService, $deploymentData);
    }

    private function createCaddyDeploymentData(Network $network, Node $node): DeploymentData
    {
        return DeploymentData::validateAndCreate([
            'networkName' => $network->docker_name,
            'internalDomain' => 'ptah.local',
            'processes' => [
                $this->getCaddyProcessConfig($node),
                $this->getDockerRegistryProcessConfig($node),
            ],
        ]);
    }

    private function getCaddyProcessConfig(Node $node): array
    {
        return [
            'name' => 'caddy',
            'placementNodeId' => $node->id,
            'workers' => [
                [
                    'name' => 'main',
                    'source' => [
                        'type' => 'docker_image',
                        'docker' => [
                            'image' => 'ghcr.io/ptah-sh/ptah-caddy:latest',
                        ],
                    ],
                    'command' => 'sh /start.sh',
                    'replicas' => 1,
                    'launchMode' => LaunchMode::Daemon->value,
                    'releaseCommand' => [
                        'command' => null,
                    ],
                    'healthcheck' => [
                        'command' => null,
                        'interval' => 10,
                        'timeout' => 5,
                        'retries' => 3,
                        'startPeriod' => 30,
                        'startInterval' => 5,
                    ],
                ],
            ],
            'envVars' => [
                [
                    'name' => 'CADDY_ADMIN',
                    'value' => '0.0.0.0:2019',
                ],
            ],
            'secretVars' => [],
            'configFiles' => [
                [
                    'path' => '/ptah/caddy/tls/.keep',
                    'content' => '# Keep this file',
                ],
                [
                    'path' => '/start.sh',
                    'content' => trim(file_get_contents(resource_path('support/caddy/start.sh'))),
                ],
            ],
            'secretFiles' => [],
            'volumes' => [
                [
                    'id' => ResourceId::make('volume'),
                    'name' => 'data',
                    'path' => '/data',
                ],
                [
                    'id' => ResourceId::make('volume'),
                    'name' => 'config',
                    'path' => '/config',
                ],
            ],
            'ports' => [
                ['targetPort' => '80', 'publishedPort' => '80'],
                ['targetPort' => '443', 'publishedPort' => '443'],
                ['targetPort' => '2019', 'publishedPort' => '2019'],
            ],
            'caddy' => [],
            'fastcgiVars' => null,
            'redirectRules' => [],
            'rewriteRules' => [],
        ];
    }

    private function getDockerRegistryProcessConfig(Node $node): array
    {
        return [
            'name' => 'registry',
            'placementNodeId' => $node->id,
            'workers' => [
                [
                    'name' => 'main',
                    'source' => [
                        'type' => 'docker_image',
                        'docker' => [
                            'image' => 'registry:latest',
                        ],
                    ],
                    'releaseCommand' => [
                        'command' => null,
                    ],
                    'healthcheck' => [
                        'command' => null,
                        'interval' => 10,
                        'timeout' => 5,
                        'retries' => 3,
                        'startPeriod' => 30,
                        'startInterval' => 5,
                    ],
                    'replicas' => 1,
                    'launchMode' => LaunchMode::Daemon->value,
                ],
                [
                    'name' => 'garbage-collector',
                    'source' => [
                        'type' => 'docker_image',
                        'docker' => [
                            'image' => 'registry:latest',
                        ],
                    ],
                    'releaseCommand' => [
                        'command' => null,
                    ],
                    'healthcheck' => [
                        'command' => null,
                        'interval' => 10,
                        'timeout' => 5,
                        'retries' => 3,
                        'startPeriod' => 30,
                        'startInterval' => 5,
                    ],
                    'replicas' => 0,
                    'command' => 'sh /ptah/prune_stale_data.sh',
                    'launchMode' => LaunchMode::Cronjob->value,
                    'crontab' => '30 0 * * *',
                ],
            ],
            'envVars' => [],
            'secretVars' => [],
            'configFiles' => [
                [
                    'path' => '/etc/docker/registry/config.yml',
                    'content' => file_get_contents(resource_path('support/registry/config.yml')),
                ],
                [
                    'path' => '/ptah/prune_stale_data.sh',
                    'content' => file_get_contents(resource_path('support/registry/prune_stale_data.sh')),
                ],
            ],
            'secretFiles' => [],
            'volumes' => [
                [
                    'id' => ResourceId::make('volume'),
                    'name' => 'data',
                    'path' => '/var/lib/registry',
                ],
            ],
            'ports' => [
                [
                    'targetPort' => 5050,
                    'publishedPort' => 5050,
                ],
            ],
            'caddy' => [],
            'fastcgiVars' => null,
            'redirectRules' => [],
            'rewriteRules' => [],
        ];
    }
}
