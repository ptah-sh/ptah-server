<?php

namespace App\Actions\Services;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\Deployment;
use App\Models\DeploymentData;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class StartDeployment
{
    use AsAction;

    public function rules(): array
    {
        return DeploymentData::getValidationRules(Request::all());
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }

    public function handle(User $user, Service $service, DeploymentData $deploymentData): Deployment
    {
        return DB::transaction(function () use ($service, $deploymentData, $user) {
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

            RebuildCaddy::run($service->team, $taskGroup, $deployment);

            return $deployment;
        });
    }

    public function asController(Service $service, ActionRequest $request): Response
    {
        $deploymentData = DeploymentData::make($request->validated());

        $team = $service->team;
        $quotas = $team->quotas();

        $quotas->deployments->ensureQuota();

        $this->handle($request->user(), $service, $deploymentData);

        return to_route('services.deployments', $service);
    }
}
