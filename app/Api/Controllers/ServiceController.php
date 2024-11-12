<?php

namespace App\Api\Controllers;

use App\Actions\Services\StartDeployment;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function deploy(Service $service, Request $request): array
    {
        Gate::authorize('deploy', $service);

        return DB::transaction(function () use ($request, $service) {
            $deploymentData = $service->latestDeployment->data->copyWith($request->all());

            $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::LaunchService);

            $deployment = StartDeployment::run($taskGroup, $service, $deploymentData);

            return [
                'deployment_id' => $deployment->id,
            ];
        });
    }
}
