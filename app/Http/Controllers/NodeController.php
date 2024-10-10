<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNodeRequest;
use App\Http\Requests\UpdateNodeRequest;
use App\Models\AgentRelease;
use App\Models\Node;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use App\Services\Metrics;
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

        $query = <<<'QUERY'
        union(
            alias(round(rate(ptah_node_cpu_user + ptah_node_cpu_system) / rate(ptah_node_cpu_total) * 100), "cpu_usage"),
            alias(round(ptah_node_memory_used_bytes / ptah_node_memory_total_bytes * 100), "memory_usage"),
            alias(round(ptah_node_disk_used_bytes{path="/"} / ptah_node_disk_total_bytes{path="/"} * 100), "disk_usage"),
            round({__name__=~"ptah_node_load_avg_(1|5|15)m"}, 0.01),
        )
        QUERY;

        $nodeIds = $nodes->pluck('id')->toArray();

        $metrics = Metrics::getMetrics($query, $nodeIds);

        return Inertia::render('Nodes/Index', [
            'nodes' => $nodes,
            'nodesLimitReached' => auth()->user()->currentTeam->quotas()->nodes->quotaReached(),
            'metrics' => $metrics,
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

        return to_route('nodes.settings', ['node' => $node->id]);
    }

    public function show(Node $node)
    {
        $query = <<<'QUERY'
        union(
            alias(round(rate(ptah_node_cpu_user + ptah_node_cpu_system) / rate(ptah_node_cpu_total) * 100), "cpu_usage"),
            alias(round(ptah_node_memory_used_bytes / ptah_node_memory_total_bytes * 100), "memory_usage"),
            alias(round(ptah_node_swap_used_bytes / ptah_node_swap_total_bytes * 100), "swap_usage"),
            alias(round(ptah_node_disk_used_bytes{path="/"} / ptah_node_disk_total_bytes{path="/"} * 100), "disk_usage"),
            alias(round(rate(ptah_node_network_rx_bytes / 1024)), "network_rx_bytes"),
            alias(round(rate(ptah_node_network_tx_bytes / 1024)), "network_tx_bytes"),
            alias(round(sum(increase(ptah_caddy_http_requests_count)) by (status_code)), "http_requests_count"),
            alias(sum(increase(ptah_caddy_http_requests_duration_bucket)) by (le), "http_requests_duration"),
        )
        QUERY;

        $metrics = Metrics::getMetricsRange($query, [$node->id]);

        return Inertia::render('Nodes/Show', [
            'node' => $node,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function settings(Node $node)
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

        return Inertia::render('Nodes/Settings', [
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
