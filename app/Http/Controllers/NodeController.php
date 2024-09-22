<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNodeRequest;
use App\Http\Requests\UpdateNodeRequest;
use App\Models\AgentRelease;
use App\Models\Node;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
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

        return Inertia::render('Nodes/Index', [
            'nodes' => $nodes,
            'nodesLimitReached' => auth()->user()->currentTeam->quotas()->nodes->quotaReached(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->currentTeam->quotas()->nodes->quotaReached()) {
            return redirect()->route('nodes.index');
        }

        return Inertia::render('Nodes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNodeRequest $request)
    {
        if (auth()->user()->currentTeam->quotas()->nodes->quotaReached()) {
            return redirect()->route('nodes.index');
        }

        $node = Node::make($request->validated());

        DB::transaction(function () use ($node) {
            $node->team_id = auth()->user()->current_team_id;
            $node->save();
        });

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

        $joinTaskGroup = $node->actualTaskGroup(NodeTaskGroupType::JoinSwarm);
        if ($joinTaskGroup?->is_completed) {
            $joinTaskGroup = null;
        }

        $lastAgentVersion = AgentRelease::latest()->first()?->tag_name;

        $taskGroup = $node->actualTaskGroup(NodeTaskGroupType::SelfUpgrade);

        $node->load('swarm');

        $s3TaskGroup = $node->actualTaskGroup(NodeTaskGroupType::UpdateS3Storages);
        if ($s3TaskGroup?->is_completed) {
            $s3TaskGroup = null;
        }

        $registryTaskGroup = $node->actualTaskGroup(NodeTaskGroupType::UpdateDockerRegistries);
        if ($registryTaskGroup?->is_completed) {
            $registryTaskGroup = null;
        }

        return Inertia::render('Nodes/Show', [
            'node' => $node,
            'isLastNode' => $node->team->nodes->count() === 1,
            'initTaskGroup' => $initTaskGroup ?: $joinTaskGroup,
            'lastAgentVersion' => $lastAgentVersion,
            'agentUpgradeTaskGroup' => $taskGroup?->is_completed ? null : $taskGroup,
            'registryUpdateTaskGroup' => $registryTaskGroup?->is_completed ? null : $registryTaskGroup,
            's3SUpdateTaskGroup' => $s3TaskGroup?->is_completed ? null : $s3TaskGroup,
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
        DB::transaction(function () use ($node) {
            Service::withoutEvents(function () use ($node) {
                $node->boundServices()->each(fn (Service $service) => $service->delete());
            });

            $node->delete();
        });

        return to_route('nodes.index');
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
