<?php

namespace App\Api\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function deploy(Service $service, Request $request): array
    {
        Gate::authorize('deploy', $service);

        $deploymentData = $service->latestDeployment->data->copyWith($request->all());

        $deployment = DB::transaction(function () use ($service, $deploymentData) {
            return $service->deploy($deploymentData);
        });

        return [
            'deployment_id' => $deployment->id,
        ];
    }
}
