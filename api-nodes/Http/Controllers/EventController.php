<?php

namespace ApiNodes\Http\Controllers;

use ApiNodes\Models\AgentStartedEventData;
use App\Actions\Nodes\InitCluster;
use App\Actions\Services\StartDeployment;
use App\Models\DeploymentData;
use App\Models\DeploymentData\LaunchMode;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\Worker;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\UpdateDirdConfig\UpdateDirdConfigMeta;
use App\Models\NodeTaskType;
use App\Util\ResourceId;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController
{
    public function started(Node $node, AgentStartedEventData $data)
    {
        DB::transaction(function () use ($node, $data) {
            $node->data = $data->node;
            $node->save();

            $advertiseAddr = $data->node->host->getPrivateIpv4() ?? $data->node->host->getPublicIpv4() ?? '127.0.0.1';

            $swarm = $node->swarm;
            if (! $swarm) {
                $swarm = InitCluster::run($node->team->owner, $node, $advertiseAddr);

                $demoService = $swarm->services()->create([
                    'name' => 'nginx-demo',
                    'team_id' => $node->team_id,
                ]);

                $publicAddr = $data->node->host->getPublicIpv4() ?? $data->node->host->getPrivateIpv4() ?? '127.0.0.1';

                StartDeployment::run($node->team->owner, $demoService, DeploymentData::validateAndCreate([
                    'networkName' => 'ptah_net',
                    'internalDomain' => 'demo.ptah.local',
                    'processes' => [
                        Process::make([
                            'name' => 'nginx',
                            'workers' => [
                                Worker::make([
                                    'name' => 'main',
                                    'source' => [
                                        'type' => 'docker_image',
                                        'docker' => [
                                            'image' => 'nginxdemos/hello',
                                        ],
                                    ],
                                    'replicas' => 1,
                                    'launchMode' => LaunchMode::Daemon->value,
                                ])->toArray(),
                            ],
                            'caddy' => [
                                [
                                    'id' => ResourceId::make('nginx-demo'),
                                    'targetProtocol' => 'http',
                                    'targetPort' => 80,
                                    'publishedPort' => 443,
                                    'domain' => $publicAddr === '127.0.0.1' ? 'localhost' : $publicAddr,
                                    'path' => '/',
                                ],
                            ],
                        ])->toArray(),
                    ],
                ]));
            }

            if ($data->swarm) {
                $swarm->data->joinTokens = $data->swarm->joinTokens;
                $swarm->data->managerNodes = $data->swarm->managerNodes;
                $swarm->data->encryptionKey = $data->swarm->encryptionKey;

                $nodeAddresses = $swarm->nodes->pluck('data.address')->toArray();
                $dockerServices = collect($swarm->services)
                    ->filter(function ($service) {
                        // TODO: use some flags on the service to determine if it should be passed to DIRD
                        return Str::contains($service->docker_name, 'caddy');
                    })
                    ->pluck('docker_name')
                    ->toArray();

                $swarm->nodes->each(function (Node $node) use ($swarm, $nodeAddresses, $dockerServices) {
                    $taskGroup = NodeTaskGroup::create([
                        'type' => NodeTaskGroupType::UpdateDirdConfig,
                        'swarm_id' => $swarm->id,
                        'team_id' => $node->team_id,
                        'node_id' => $node->id,
                        'invoker_id' => $swarm->team->user_id,
                    ]);

                    $meta = new UpdateDirdConfigMeta(
                        nodeAddresses: $nodeAddresses,
                        dockerServices: $dockerServices,
                        nodePorts: ['tcp/80', 'tcp/443']
                    );

                    $taskGroup->tasks()->create([
                        'type' => NodeTaskType::UpdateDirdConfig,
                        'meta' => $meta,
                        'payload' => [
                            'NodeAddresses' => $nodeAddresses,
                            'DockerServices' => $dockerServices,
                            'NodePorts' => ['tcp/80', 'tcp/443'],
                        ],
                    ]);
                });

                $swarm->save();
            }
        });

        return [
            'settings' => [
                'poll_interval' => 5,
            ],
        ];
    }
}
