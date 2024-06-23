<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\DeploymentData;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use App\Models\Swarm;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Services/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $swarms = Swarm::all();

        $networks = count($swarms) ? $swarms[0]->networks : [];
        $nodes = count($swarms) ? $swarms[0]->nodes : [];

        return Inertia::render('Services/Create', [
            'swarms' => $swarms,
            'networks' => $networks,
            'nodes' => $nodes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $deploymentData = DeploymentData::validateAndCreate($request->all());

        $service = Service::create($request->validated());

        $taskGroup = NodeTaskGroup::create([
            'swarm_id' => $service->swarm_id,
            'invoker_id' => auth()->id(),
            'type' => NodeTaskGroupType::DeployService,
        ]);

        $deployment = $service->deployments()->create([
            'task_group_id' => $taskGroup->id,
            'data' => $deploymentData,
        ]);

        $taskGroup->tasks()->createMany($deployment->asNodeTasks());

        return to_route('services.deployments', ['service' => $service->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return Inertia::render('Services/Show', ['service' => $service]);
    }

    public function deployments(Service $service)
    {
        return Inertia::render('Services/Deployments', ['service' => $service]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
