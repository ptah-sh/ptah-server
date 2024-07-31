<?php

namespace ApiNodes\Http\Controllers;

use ApiNodes\Models\AgentStartedEventData;
use App\Models\Node;
use Illuminate\Support\Facades\DB;

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
