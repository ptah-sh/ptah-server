<?php

namespace ApiNodes\Http\Controllers;

use ApiNodes\Models\AgentStartedEventData;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\UpdateDirdConfig\UpdateDirdConfigMeta;
use App\Models\NodeTaskType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController
{
    public function started(Node $node, AgentStartedEventData $data)
    {
        DB::transaction(function () use ($node, $data) {
            $node->data = $data->node;
            $node->save();

            if ($node->swarm && $data->swarm) {
                $swarm = $node->swarm;

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
