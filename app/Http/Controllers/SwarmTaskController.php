<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeTask\InitClusterFormRequest;
use App\Http\Requests\NodeTask\JoinClusterFormRequest;
use App\Models\Deployment;
use App\Models\DeploymentData;
use App\Models\DeploymentData\LaunchMode;
use App\Models\DeploymentData\ReleaseCommand;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTasks\JoinSwarm\JoinSwarmMeta;
use App\Models\NodeTasks\UpdateCurrentNode\UpdateCurrentNodeMeta;
use App\Models\NodeTaskType;
use App\Models\Service;
use App\Models\Swarm;
use App\Models\SwarmData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SwarmTaskController extends Controller
{
    public function initCluster(InitClusterFormRequest $request)
    {
        DB::transaction(function () use ($request) {
            $swarm = Swarm::create([
                'name' => $request->name,
                'team_id' => auth()->user()->current_team_id,
                'data' => SwarmData::validateAndCreate([
                    'registriesRev' => 0,
                    'registries' => [],
                    's3StoragesRev' => 0,
                    's3Storages' => [],
                ]),
            ]);

            $node = Node::find($request->node_id);

            $node->swarm_id = $swarm->id;
            $node->saveQuietly();

            $network = Network::create([
                'swarm_id' => $swarm->id,
                'team_id' => auth()->user()->current_team_id,
                'name' => dockerize_name('ptah-net'),
            ]);

            $taskGroup = NodeTaskGroup::create([
                'type' => NodeTaskGroupType::InitSwarm,
                'swarm_id' => $swarm->id,
                'node_id' => $request->node_id,
                'invoker_id' => auth()->user()->id,
                'team_id' => auth()->user()->current_team_id,
            ]);

            $tasks = [
                [
                    'type' => NodeTaskType::InitSwarm,
                    'meta' => InitSwarmMeta::from([
                        'swarmId' => $swarm->id,
                        'name' => $swarm->name,
                        'forceNewCluster' => $request->force_new_cluster,
                    ]),
                    'payload' => [
                        'SwarmInitRequest' => [
                            'ForceNewCluster' => $request->force_new_cluster,
                            'ListenAddr' => '0.0.0.0:2377',
                            'AdvertiseAddr' => $request->advertise_addr,
                            'Spec' => [
                                'Annotations' => [
                                    'Name' => $request->name,
                                    'Labels' => dockerize_labels([
                                        'swarm.id' => $swarm->id,
                                    ]),
                                ]
                            ]
                        ]
                    ]
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
                        ]
                    ]
                ],
                [
                    'type' => NodeTaskType::CreateNetwork,
                    'meta' => CreateNetworkMeta::from(['networkId' => $network->id, 'name' => $network->name]),
                    'payload' => [
                        'NetworkName' => $network->docker_name,
                        'NetworkCreateOptions' => [
                            'Driver' => 'overlay',
                            'Labels' => dockerize_labels([
                                'network.id' => $network->id
                            ]),
                            'Scope' => 'swarm',
                            'Attachable' => true
                        ],
                    ],
                ],
            ];

            $caddyService = $swarm->services()->create([
                'name' => 'caddy',
                'team_id' => auth()->user()->current_team_id,
            ]);

            $deployment = $caddyService->deployments()->create([
                'team_id' => auth()->user()->current_team_id,
                'data' => DeploymentData::validateAndCreate([
                    'networkName' => $network->docker_name,
                    'internalDomain' => 'caddy.ptah.local',
                    'placementNodeId' => $node->id,
                    'processes' => [
                        [
                            'name' => 'svc',
                            'launchMode' => LaunchMode::Daemon->value,
                            'dockerRegistryId' => null,
                            'dockerImage' => 'caddy:2.8-alpine',
                            'releaseCommand' => [
                                'command' => null,
                            ],
                            'command' => 'sh /start.sh',
                            'backups' => [],
                            'workers' => [],
                            'envVars' => [
                                [
                                    'name' => 'CADDY_ADMIN',
                                    'value' => '0.0.0.0:2019',
                                ]
                            ],
                            'secretVars' => [
                                'vars' => [],
                            ],
                            'configFiles' => [
                                [
                                    'path' => '/ptah/caddy/tls/.keep',
                                    'content' => '# Keep this file',
                                ],
                                [
                                    'path' => '/start.sh',
                                    'content' => trim(file_get_contents(resource_path('support/caddy/start.sh'))),
                                ]
                            ],
                            'secretFiles' => [],
                            'volumes' => [
                                [
                                    'id' => 'volume-' . Str::random(11),
                                    'name' => 'data',
                                    'path' => '/data',
                                ],
                                [
                                    'id' => 'volume-' . Str::random(11),
                                    'name' => 'config',
                                    'path' => '/config',
                                ]
                            ],
                            'ports' => [
                                [
                                    'targetPort' => '80',
                                    'publishedPort' => '80',
                                ],
                                [
                                    'targetPort' => '443',
                                    'publishedPort' => '443',
                                ],
                                [
                                    'targetPort' => '2019',
                                    'publishedPort' => '2019',
                                ],
                            ],
                            'replicas' => 1,
                            'caddy' => [],
                            'fastcgiVars' => null,
                            'redirectRules' => [],
                        ],
                    ],
                ]),
            ]);
            $deployment->team_id = auth()->user()->current_team_id;
            $deployment->save();

            foreach ($deployment->asNodeTasks() as $task) {
                $tasks[] = $task;
            }

            $taskGroup->tasks()->createMany($tasks);
        });
    }

    public function joinCluster(JoinClusterFormRequest $request)
    {
        DB::transaction(function () use ($request) {
            $taskGroup = NodeTaskGroup::create([
                'type' => NodeTaskGroupType::JoinSwarm,
                'swarm_id' => $request->swarm_id,
                'team_id' => auth()->user()->current_team_id,
                'node_id' => $request->node_id,
                'invoker_id' => auth()->user()->id,
            ]);

            $node = Node::findOrFail($request->node_id);
            $node->swarm_id = $request->swarm_id;
            $node->save();

            $remoteAddrs = collect($taskGroup->swarm->data->managerNodes)->map(fn(SwarmData\ManagerNode $node) => $node->addr)->toArray();

            $joinToken = match ($request->role) {
                'manager' => $taskGroup->swarm->data->joinTokens->manager,
                'worker' => $taskGroup->swarm->data->joinTokens->worker,
            };

            $taskGroup->tasks()->create([
                'type' => NodeTaskType::JoinSwarm,
                'meta' => JoinSwarmMeta::from(['swarmId' => $request->swarm_id, 'role' => $request->role]),
                'payload' => [
                    'JoinSpec' => [
                        'ListenAddr' => '0.0.0.0:2377',
                        'AdvertiseAddr' => $request->advertise_addr,
                        'DataPathAddr' => $request->advertise_addr,
                        'RemoteAddrs' => $remoteAddrs,
                        'JoinToken' => $joinToken,
                        'Availability' => 'active',
                    ],
                ]
            ]);
        });
    }
}
