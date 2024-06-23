<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeTask\InitClusterFormRequest;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\CreateNetwork\CreateNetworkMeta;
use App\Models\NodeTasks\InitSwarm\InitSwarmMeta;
use App\Models\NodeTaskType;
use App\Models\Swarm;

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
            'name' => 'ptah-net',
        ]);

        $taskGroup = NodeTaskGroup::create([
            'type' => NodeTaskGroupType::InitSwarm,
            'swarm_id' => $swarm->id,
            'node_id' => $request->node_id,
            'invoker_id' => auth()->user()->id,
        ]);

        $taskGroup->tasks()->createMany([
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
                    'NetworkName' => $network->name,
                    'NetworkCreateOptions' => [
                        'Driver' => 'overlay',
                        'Labels' => dockerize_labels([
                            'network.id' => $network->id
                        ]),
                    ],
                ],
            ],
            // TODO: create bare-bones Caddy
//            [
//                'type' => NodeTaskType::CreateService,
//            ]
        ]);
    }
}
