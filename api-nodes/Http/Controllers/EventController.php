<?php

namespace ApiNodes\Http\Controllers;

use App\Models\Node;
use App\Models\NodeData;
use Illuminate\Http\Request;

class EventController
{
    public function started(Node $node, NodeData $data)
    {
        $node->data = $data;

        $node->save();

        return [
            'settings' => [
                'poll_interval' => 5
            ]
        ];
    }
}