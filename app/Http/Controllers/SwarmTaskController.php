<?php

namespace App\Http\Controllers;

use App\Http\Requests\NodeTask\JoinClusterFormRequest;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\JoinSwarm\JoinSwarmMeta;
use App\Models\NodeTaskType;
use App\Models\SwarmData;
use Illuminate\Support\Facades\DB;

class SwarmTaskController extends Controller
{
    public function joinCluster(JoinClusterFormRequest $request)
    {
        DB::transaction(function () use ($request) {
            $node = Node::findOrFail($request->node_id);
            $swarm = $node->team->swarms()->first();

            if (! $swarm) {
                throw new \Exception('No swarm found for the node\'s team.');
            }

            $taskGroup = NodeTaskGroup::create([
                'type' => NodeTaskGroupType::JoinSwarm,
                'swarm_id' => $swarm->id,
                'team_id' => $node->team_id,
                'node_id' => $node->id,
                'invoker_id' => auth()->user()->id,
            ]);

            $node->swarm_id = $swarm->id;
            $node->save();

            $remoteAddrs = collect($taskGroup->swarm->data->managerNodes)->map(fn (SwarmData\ManagerNode $node) => $node->addr)->toArray();

            $joinToken = match ($request->role) {
                'manager' => $taskGroup->swarm->data->joinTokens->manager,
                'worker' => $taskGroup->swarm->data->joinTokens->worker,
            };

            $taskGroup->tasks()->create([
                'type' => NodeTaskType::JoinSwarm,
                'meta' => JoinSwarmMeta::from(['swarmId' => $swarm->id, 'role' => $request->role]),
                'payload' => [
                    'JoinSpec' => [
                        'ListenAddr' => '0.0.0.0:2377',
                        'AdvertiseAddr' => $request->advertise_addr,
                        'DataPathAddr' => $request->advertise_addr,
                        'RemoteAddrs' => $remoteAddrs,
                        'JoinToken' => $joinToken,
                        'Availability' => 'active',
                    ],
                ],
            ]);
        });
    }
}
