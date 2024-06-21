<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class NodeTaskGroupController extends Controller
{
    public function retry(Request $request, NodeTaskGroup $taskGroup)
    {
        $this->authorizeOr403('retry', $taskGroup);

        $attrs = $request->validate([
            'node_id' => 'required|exists:nodes,id',
        ]);

        $node = Node::whereId($attrs['node_id'])->first();

        $this->authorizeOr403('view', $node);

        $taskGroup->retry($node);
    }
}