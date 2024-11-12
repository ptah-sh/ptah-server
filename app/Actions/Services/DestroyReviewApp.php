<?php

namespace App\Actions\Services;

use App\Actions\Nodes\RebuildCaddy;
use App\Models\NodeTaskGroup;
use App\Models\ReviewApp;
use Illuminate\Support\Facades\DB;

class DestroyReviewApp
{
    public function destroy(NodeTaskGroup $taskGroup, ReviewApp $reviewApp): void
    {
        DB::transaction(function () use ($reviewApp, $taskGroup) {
            RebuildCaddy::onceAfter($reviewApp->team, $taskGroup, function () use ($reviewApp, $taskGroup) {
                (new UndeployDeployment)->undeploy($reviewApp->latestDeployment, $taskGroup);
            });

            $reviewApp->delete();
        });
    }
}
