<?php

namespace App\Http\Controllers;

use App\Actions\Services\DestroyService;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\DeploymentData;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\Worker;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use App\Models\Swarm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::with('latestDeployment', 'latestDeployment.latestTaskGroup')->orderBy('name')->get();

        $swarmExists = Swarm::exists();

        // Check if the quota is reached using ItemQuota
        $team = auth()->user()->currentTeam;
        $serviceQuota = $team->quotas()->services;
        $quotaReached = $serviceQuota->quotaReached();

        $hasOnlineNodes = Node::online()->exists();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'swarmExists' => $swarmExists,
            'hasOnlineNodes' => $hasOnlineNodes,
            'quotaReached' => $quotaReached,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $swarm = auth()->user()->currentTeam->swarms()->first();

        $networks = $swarm->networks;
        $nodes = $swarm->nodes;
        $dockerRegistries = $swarm->data->registries;
        $s3Storages = $swarm->data->s3Storages;

        $deploymentData = DeploymentData::make([
            'networkName' => $networks->first()->name,
        ]);

        $blankProcess = Process::make([]);
        $blankWorker = Worker::make([]);

        return Inertia::render('Services/Create', [
            'networks' => $networks,
            'nodes' => $nodes,
            'deploymentData' => $deploymentData,
            'dockerRegistries' => $dockerRegistries,
            's3Storages' => $s3Storages,
            'marketplaceUrl' => config('ptah.marketplace_url'),
            'node' => [
                'swarm' => $swarm,
            ],
            'blankProcess' => $blankProcess,
            'blankWorker' => $blankWorker,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load(['latestDeployment', 'team', 'swarm']);

        $networks = $service->swarm->networks;
        $nodes = $service->swarm->nodes;
        $dockerRegistries = $service->swarm->data->registries;
        $s3Storages = $service->swarm->data->s3Storages;
        $swarm = $service->swarm;

        $blankProcess = Process::make([]);
        $blankWorker = Worker::make([]);

        return Inertia::render('Services/Show', [
            'service' => $service,
            'networks' => $networks,
            'nodes' => $nodes,
            'dockerRegistries' => $dockerRegistries,
            's3Storages' => $s3Storages,
            'node' => [
                'swarm' => $swarm,
            ],
            'blankProcess' => $blankProcess,
            'blankWorker' => $blankWorker,
        ]);
    }

    public function deployments(Service $service)
    {
        $service->load([
            'deployments.reviewApp',
            'deployments' => function ($deployments) {
                $deployments->with(['latestTaskGroup' => function ($taskGroups) {
                    $taskGroups->with([
                        'invoker',
                        'tasks' => function ($tasks) {}]);
                }]);
            }]);

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
        $service->update($request->validated());

        session()->flash('success', 'Service updated successfully!');

        return to_route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service, Request $request)
    {
        DB::transaction(function () use ($request, $service) {
            $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::DeleteService);

            (new DestroyService)->destroy($taskGroup, $service);
        });

        session()->flash('success', "Service '{$service->name}' deleted successfully!");

        return to_route('services.index');
    }
}
