<?php

namespace App\Actions\Services;

use App\Models\DeploymentData;
use App\Models\DeploymentData\Caddy;
use App\Models\NodeTaskGroup;
use App\Models\ReviewApp;
use App\Models\ReviewApps\ReviewAppMeta;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class LaunchReviewApp
{
    public function launch(NodeTaskGroup $taskGroup, Service $service, ReviewAppMeta $meta, array $process, array $worker): ReviewApp
    {
        $taskGroup->team->quotas()->reviewApps->ensureQuota();

        return DB::transaction(function () use ($taskGroup, $service, $meta, $process, $worker) {
            $latestDeployment = $service->latestDeployment->data;

            // TODO: each request creates a new review app service !!!
            $deploymentData = $latestDeployment->fork($meta->ref, $process, $worker);

            $reviewApp = $service->reviewApps()->updateOrCreate([
                'team_id' => $service->team_id,
                'process' => $process['name'],
                'worker' => $worker['name'],
                'ref' => $meta->ref,
            ], [
                'ref_url' => $meta->refUrl,
                'visit_url' => $this->getVisitUrl($deploymentData, $process),
            ]);

            StartDeployment::run($taskGroup, $service, $deploymentData, $reviewApp);

            return $reviewApp;
        });
    }

    private function getVisitUrl(DeploymentData $deploymentData, array $process): ?string
    {
        $caddys = $deploymentData->findProcessByName($process['name'])->caddy;
        if (count($caddys) > 0) {
            /** @var Caddy $caddy */
            $caddy = $caddys[0];

            $schema = $caddy->publishedPort === 80 ? 'http' : 'https';

            return "{$schema}://{$caddy->domain}";
        }

        return null;
    }
}
