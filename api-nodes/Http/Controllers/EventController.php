<?php

namespace ApiNodes\Http\Controllers;

use ApiNodes\Models\AgentStartedEventData;
use App\Models\Node;
use App\Models\NodeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                'poll_interval' => 5
            ]
        ];
    }
}