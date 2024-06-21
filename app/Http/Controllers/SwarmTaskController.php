<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeTask\InitClusterFormRequest;
use App\Models\Network;
use App\Models\Node;
use App\Models\NodeTask\CreateNetworkTaskMeta;
use App\Models\NodeTask\CreateNetworkTaskPayload;
use App\Models\NodeTask\InitSwarmTaskMeta;
use App\Models\NodeTask\InitSwarmTaskPayload;
use App\Models\NodeTaskGroup;
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
            'name' => 'ptah-net',
        ]);

        $taskGroup = NodeTaskGroup::create([
            'swarm_id' => $swarm->id,
            'node_id' => $request->node_id,
            'invoker_id' => auth()->user()->id,
        ]);

        $taskGroup->tasks()->createMany([
            [
                'meta' => new CreateNetworkTaskMeta($network->id),
                'payload' => new CreateNetworkTaskPayload('ptah-net'),
            ],
            [
                'meta' => new InitSwarmTaskMeta($swarm->id),
                'payload' => new InitSwarmTaskPayload($request->name, $request->force_new_cluster),
            ]
        ]);
    }
}