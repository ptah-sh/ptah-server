<?php

namespace App\Actions\Services;

use App\Models\DeploymentData;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StartDeployment
{
    use AsAction;

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'deployment_data' => ['required', DeploymentData::class],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $service = Service::with('team')->findOrFail($request->service_id);

        return $request->user()->belongsToTeam($service->team);
    }

    public function handle(User $user, Service $service, DeploymentData $deploymentData)
    {
        DB::transaction(function () use ($service, $deploymentData, $user) {
            $service->placement_node_id = $deploymentData->placementNodeId;
            $service->saveQuietly();

            $taskGroup = NodeTaskGroup::create([
                'swarm_id' => $service->swarm_id,
                'team_id' => $service->team_id,
                'invoker_id' => $user->id,
                'type' => $service->deployments()->exists() ? NodeTaskGroupType::UpdateService : NodeTaskGroupType::CreateService,
            ]);

            $deployment = $service->deployments()->create([
                'team_id' => $service->team_id,
                'data' => $deploymentData,
            ]);

            $taskGroup->tasks()->createMany($deployment->asNodeTasks());
        });
    }

    public function asController(ActionRequest $request)
    {
        $service = Service::findOrFail($request->service_id);
        $deploymentData = DeploymentData::make($request->validated());

        $this->handle($request->user(), $service, $deploymentData);
    }
}
