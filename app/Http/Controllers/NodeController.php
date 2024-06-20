<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNodeRequest;
use App\Http\Requests\UpdateNodeRequest;
use App\Models\Node;
use App\Models\NodeTask;
use App\Models\NodeTask\InitSwarmTaskPayload;
use Inertia\Inertia;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Nodes/Index');
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
        $node = Node::create($request->all());

        return to_route('nodes.show', ['node' => $node->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Node $node)
    {
        $initTaskGroup = $node->tasks()->inProgress()->ofType(InitSwarmTaskPayload::class)->first()?->taskGroup->with('tasks')->first();
        if (!$initTaskGroup) {
            $initTaskGroup = $node->tasks()->failed()->ofType(InitSwarmTaskPayload::class)->first()?->taskGroup->with('tasks')->first();
        }

        return Inertia::render('Nodes/Show', ['node' => $node, 'initTaskGroup' => $initTaskGroup]);
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
}
