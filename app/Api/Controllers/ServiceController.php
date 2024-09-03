<?php

namespace App\Api\Controllers;

use App\Actions\Services\StartDeployment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function deploy(Service $service, Request $request): array
    {
        Gate::authorize('deploy', $service);

        $deploymentData = $service->latestDeployment->data->copyWith($request->all());

        $deployment = StartDeployment::run(auth()->user(), $service, $deploymentData);

        return [
            'deployment_id' => $deployment->id,
        ];
    }
}
