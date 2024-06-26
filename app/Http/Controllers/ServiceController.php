<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\DeploymentData;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use App\Models\Swarm;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::with(['latestDeployment' => function ($query) {
            $query->with(['latestTaskGroup' => function ($query) {
                $query->with('latestTask');
            }]);
        }])->orderBy('name')->get();

        return Inertia::render('Services/Index', ['services' => $services]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $swarms = Swarm::all();

        $networks = count($swarms) ? $swarms[0]->networks : [];
        $nodes = count($swarms) ? $swarms[0]->nodes : [];

        $deploymentData = DeploymentData::make([
            'networkName' => count($networks) ? $networks[0]->name : null,
        ]);

        return Inertia::render('Services/Create', [
            'swarms' => $swarms,
            'networks' => $networks,
            'nodes' => $nodes,
            'deploymentData' => $deploymentData,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $deploymentData = DeploymentData::validateAndCreate($request->get('deploymentData'));

        $service = Service::make($request->validated());
        DB::transaction(function () use ($service, $deploymentData) {
            $service->save();

            $service->deploy($deploymentData);
        });


        return to_route('services.deployments', ['service' => $service->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load(['latestDeployment', 'team', 'swarm']);

        $networks = $service->swarm->networks;
        $nodes = $service->swarm->nodes;

        return Inertia::render('Services/Show', ['service' => $service, 'networks' => $networks, 'nodes' => $nodes]);
    }

    public function deployments(Service $service)
    {
        $service->load(['deployments' => function ($deployments) {
            $deployments->with(['latestTaskGroup' => function ($taskGroups) {
                $taskGroups->with([
                    'invoker',
                    'tasks' => function ($tasks) {
                }]);
            }]);
        }]);

        return Inertia::render('Services/Deployments', ['service' => $service]);
    }

    public function deploy(Service $service, DeploymentData $deploymentData)
    {
        DB::transaction(function() use ($service, $deploymentData) {
            $service->deploy($deploymentData);
        });

        return to_route('services.deployments', ['service' => $service->id]);
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
        $service->update($request->validated());

        session()->flash('success', 'Service updated successfully!');

        return to_route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        DB::transaction(function () use ($service) {
            $service->delete();
        });

        session()->flash('success', "Service '{$service->name}' deleted successfully!");

        return to_route('services.index');
    }
}
