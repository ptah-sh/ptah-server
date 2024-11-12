<?php

namespace App\Actions\Services;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\Deployment;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\Worker;
use App\Models\NodeTaskGroup;
use App\Models\NodeTasks\DeleteService\DeleteServiceMeta;
use App\Models\NodeTaskType;
use Illuminate\Support\Facades\DB;

class UndeployDeployment
{
    public function undeploy(Deployment $deployment, NodeTaskGroup $taskGroup): void
    {
        DB::transaction(function () use ($deployment, $taskGroup) {
            $tasks = collect($deployment->data->processes)->flatMap(function (Process $process) use ($deployment) {
                return collect($process->workers)->map(function (Worker $worker) use ($deployment) {
                    return [
                        'type' => NodeTaskType::DeleteService,
                        'meta' => new DeleteServiceMeta($deployment->service->id, $worker->name, $deployment->service->name),
                        'payload' => [
                            'ServiceName' => $worker->dockerName,
                        ],
                    ];
                });
            })->toArray();

            $taskGroup->tasks()->createMany($tasks);

            RebuildCaddy::onceAfter($deployment->team, $taskGroup, function () {
                // Intentionally left blank
            });
        });
    }
}
