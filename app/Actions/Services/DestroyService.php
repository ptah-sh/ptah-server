<?php

namespace App\Actions\Services;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\NodeTaskGroup;
use App\Models\ReviewApp;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DestroyService
{
    public function destroy(NodeTaskGroup $taskGroup, Service $service): void
    {
        DB::transaction(function () use ($taskGroup, $service) {
            RebuildCaddy::onceAfter($service->team, $taskGroup, function () use ($service, $taskGroup) {
                $service->reviewApps->each(function (ReviewApp $reviewApp) use ($taskGroup) {
                    (new DestroyReviewApp)->destroy($taskGroup, $reviewApp);
                });

                (new UndeployDeployment)->undeploy($service->latestDeployment, $taskGroup);
            });

            $service->delete();
        });
    }
}
