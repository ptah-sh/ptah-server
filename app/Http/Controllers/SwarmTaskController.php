<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeTask\InitClusterFormRequest;
use App\Models\DeploymentData;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\CreateConfig\CreateConfigMeta;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTaskType;
use App\Models\Swarm;
use Illuminate\Database\Eloquent\Casts\Json;

class SwarmTaskController extends Controller
{
    public function initCluster(InitClusterFormRequest $request)
    {
        $swarm = Swarm::create([
            'name' => $request->name,
        ]);

        Node::whereId($request->node_id)->update([
            'swarm_id' => $swarm->id,
        ]);

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
            'task_group_id' => $taskGroup->id,
            'data' => DeploymentData::from([
                'dockerRegistryId' => null,
                'dockerImage' => 'caddy:2.8-alpine',
                'envVars' => [
                    [
                        'name' => 'CADDY_ADMIN',
                        'value' => '0.0.0.0:2019',
                    ]
                ],
                'secretVars' => [],
                'configFiles' => [
                    [
                        'path' => '/ptah/caddy/tls/.keep',
                        'content' => '# Keep this file',
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
                'networkName' => $network->docker_name,
                'internalDomain' => 'caddy.ptah.local',
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
                'placementNodeId' => null,
                'caddy' => [],
                'fastcgiVars' => [],
            ]),
        ]);

        $caddyJson = Json::encode([
            'apps' => [
                'tls' => [
                    !!111
                ]
            ]
        ]);

        $tasks[] = [
            'type' => NodeTaskType::CreateConfig,
            'meta' => CreateConfigMeta::from([
                'deploymentId' => $deployment->id,
                'path' => 'caddy base config',
                'hash' => md5($caddyJson),
            ]),
            'payload' => [
                'SwarmConfigSpec' => [
                    'Name' => $caddyService->makeResourceName('caddy_base'),
                    'Data' => base64_encode($caddyJson),
                    'Labels' => dockerize_labels([
                        'service.id' => $caddyService->id,
                        'deployment.id' => $deployment->id,
                        "content.hash" => md5($caddyJson),
                        "kind" => 'caddy',
                    ]),
                ],
            ],
        ];

        foreach ($deployment->asNodeTasks() as $task) {
            $tasks[] = $task;
        }

        $taskGroup->tasks()->createMany($tasks);
    }
}
