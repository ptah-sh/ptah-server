<?php

namespace App\Actions\Services;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\Deployment;
use App\Models\DeploymentData;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\ReviewApp;
use App\Models\Service;
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

    public function handle(NodeTaskGroup $taskGroup, Service $service, DeploymentData $deploymentData, ?ReviewApp $reviewApp = null): Deployment
    {
        $taskGroup->team->quotas()->deployments->ensureQuota();

        return DB::transaction(function () use ($taskGroup, $service, $deploymentData, $reviewApp) {
            $deployment = $service->deployments()->create([
                'team_id' => $service->team_id,
                'data' => $deploymentData,
                'configured_by_id' => $taskGroup->invoker_id,
                'review_app_id' => $reviewApp?->id,
            ]);

            $deployment->taskGroups()->attach($taskGroup);

            $taskGroup->tasks()->createMany($deployment->asNodeTasks());

            RebuildCaddy::onceAfter($service->team, $taskGroup, function () {
                // Intentionally left blank
            });

            return $deployment;
        });
    }

    public function asController(Service $service, ActionRequest $request): Response
    {
        $deploymentData = DeploymentData::make($request->validated());

        $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::LaunchService);

        $this->handle($taskGroup, $service, $deploymentData, null);

        return to_route('services.deployments', $service);
    }
}
