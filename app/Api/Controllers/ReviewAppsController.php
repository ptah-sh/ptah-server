<?php

namespace App\Api\Controllers;

use App\Actions\Services\DestroyReviewApp;
use App\Actions\Services\LaunchReviewApp;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\ReviewApps\ReviewAppMeta;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewAppsController extends Controller
{
    public function store(Service $service, Request $request)
    {
        $meta = ReviewAppMeta::validateAndCreate($request->input('meta'));

        $request->validate([
            'process' => ['required', 'array'],
            'process.name' => ['required', 'string'],
            'process.workers' => ['prohibited', 'exclude'],
            'worker' => ['required', 'array'],
            'worker.name' => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($request, $meta, $service) {
            $taskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::LaunchReviewApp);

            $reviewApp = (new LaunchReviewApp)->launch($taskGroup, $service, $meta, $request->input('process'), $request->input('worker'));

            return [
                'review_app' => [
                    'id' => $reviewApp->id,
                    'visit_url' => $reviewApp->visit_url,
                ],
                'deployment' => [
                    'id' => $reviewApp->latestDeployment->id,
                ],
            ];
        });
    }

    public function destroy(Request $request, Service $service)
    {
        $validated = $request->validate([
            'ref' => ['required', 'string'],
            'process' => ['required', 'string'],
            'worker' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($request, $validated, $service) {
            $nodeTaskGroup = NodeTaskGroup::createForUser($request->user(), $service->team, NodeTaskGroupType::DestroyReviewApp);

            $reviewApp = $service->reviewApps()->where([
                'ref' => $validated['ref'],
                'process' => $validated['process'],
                'worker' => $validated['worker'],
            ])->firstOrFail();

            (new DestroyReviewApp)->destroy($nodeTaskGroup, $reviewApp);
        });

        return response()->noContent();
    }
}
