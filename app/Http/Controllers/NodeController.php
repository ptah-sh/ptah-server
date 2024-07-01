<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNodeRequest;
use App\Http\Requests\UpdateNodeRequest;
use App\Models\AgentRelease;
use App\Models\Node;
use App\Models\NodeTaskGroupType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nodes = Node::all();

        return Inertia::render('Nodes/Index', ['nodes' => $nodes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Nodes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNodeRequest $request)
    {
        $node = Node::create($request->validated());

        return to_route('nodes.show', ['node' => $node->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Node $node)
    {
        $initTaskGroup = $node->actualTaskGroup(NodeTaskGroupType::InitSwarm);
        if ($initTaskGroup?->is_completed) {
            $initTaskGroup = null;
        }

        $lastAgentVersion = AgentRelease::latest()->first()?->tag_name;

        $taskGroup = $node->actualTaskGroup(NodeTaskGroupType::SelfUpgrade);

        return Inertia::render('Nodes/Show', [
            'node' => $node,
            'initTaskGroup' => $initTaskGroup,
            'lastAgentVersion' => $lastAgentVersion,
            'agentUpgradeTaskGroup' => $taskGroup?->is_completed ? null : $taskGroup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Node $node)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNodeRequest $request, Node $node)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Node $node)
    {
        //
    }

    public function upgradeAgent(Node $node, Request $request)
    {
        $req = $request->validate([
            'targetVersion' => ['required', 'exists:agent_releases,tag_name'],
        ]);

        DB::transaction(function () use ($node, $req) {
            $node->upgradeAgent($req['targetVersion']);
        });

        return to_route('nodes.show', ['node' => $node->id]);
    }
}
