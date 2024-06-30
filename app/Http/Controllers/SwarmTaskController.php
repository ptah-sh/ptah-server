<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeTask\InitClusterFormRequest;
use App\Models\DeploymentData;
use App\Models\DeploymentData\LaunchMode;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTasks\UpdateCurrentNode\UpdateCurrentNodeMeta;
use App\Models\NodeTaskType;
use App\Models\Swarm;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\DB;

class SwarmTaskController extends Controller
{
    public function initCluster(InitClusterFormRequest $request)
    {
        DB::transaction(function () use ($request) {
            $swarm = Swarm::create([
                'name' => $request->name,
            ]);

            $node = Node::find($request->node_id);

            $node->swarm_id = $swarm->id;
            $node->saveQuietly();

            $network = Network::create([
                'swarm_id' => $swarm->id,
                'name' => dockerize_name('ptah-net'),
            ]);

            $taskGroup = NodeTaskGroup::create([
                'type' => NodeTaskGroupType::InitSwarm,
                'swarm_id' => $swarm->id,
                'node_id' => $request->node_id,
                'invoker_id' => auth()->user()->id,
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
                        ],
                    ],
                ],
            ];

            $caddyService = $swarm->services()->create([
                'name' => 'caddy',
            ]);

            $deployment = $caddyService->deployments()->create([
                'data' => DeploymentData::validateAndCreate([
                    'networkName' => $network->docker_name,
                    'internalDomain' => 'caddy.ptah.local',
                    'placementNodeId' => null,
                    'processes' => [
                        [
                            'name' => 'caddy',
                            'launchMode' => LaunchMode::Daemon->value,
                            'dockerRegistryId' => null,
                            'dockerImage' => 'caddy:2.8-alpine',
                            'command' => 'sh /start.sh',
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
                                    'content' => file_get_contents(resource_path('support/caddy/start.sh')),
                                ]
                            ],
                            'secretFiles' => [],
                            'volumes' => [
                                [
                                    'name' => 'data',
                                    'path' => '/data',
                                ],
                                [
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
                        ],
                    ],
                ]),
            ]);

            foreach ($deployment->asNodeTasks() as $task) {
                $tasks[] = $task;
            }

            $taskGroup->tasks()->createMany($tasks);
        });
    }
}
