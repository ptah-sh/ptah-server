<?php

namespace App\Api\Controllers;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\DeploymentData\Caddy;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CaddyController extends Controller
{
    public function store(Service $service, string $process, Request $request): Response
    {
        Gate::authorize('deploy', $service);

        $caddy = Caddy::from($request->all());

        $deployment = $service->latestDeployment;

        $process = $deployment->data->findProcessByName($process);
        if (! $process) {
            throw new NotFoundHttpException('Process not found');
        }

        $process->addCaddy($caddy);

        $deployment->save();

        $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::RebuildCaddy);

        RebuildCaddy::run($service->team, $taskGroup);

        return response()->noContent();
    }

    public function update(Service $service, string $process, string $id, Request $request): Response
    {
        Gate::authorize('deploy', $service);

        $caddy = Caddy::from([
            ...$request->all(),
            'id' => $id,
        ]);

        $deployment = $service->latestDeployment;

        $process = $deployment->data->findProcessByName($process);
        if (! $process) {
            throw new NotFoundHttpException('Process not found');
        }

        $process->putCaddyById($id, $caddy);

        $deployment->save();

        $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::RebuildCaddy);

        RebuildCaddy::run($service->team, $taskGroup);

        return response()->noContent();
    }

    public function destroy(Service $service, string $process, string $id, Request $request): Response
    {
        Gate::authorize('deploy', $service);

        $deployment = $service->latestDeployment;

        $process = $deployment->data->findProcessByName($process);
        if (! $process) {
            throw new NotFoundHttpException('Process not found');
        }

        $process->removeCaddyById($id);

        $deployment->save();

        $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::RebuildCaddy);

        RebuildCaddy::run($service->team, $taskGroup);

        return response()->noContent();
    }
}
