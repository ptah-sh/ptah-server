<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;

class ReviewAppsController extends Controller
{
    public function index(Service $service)
    {
        $service->load([
            'reviewApps',
            'reviewApps.latestDeployment',
        ]);

        return Inertia::render('Services/ReviewApps', [
            'service' => $service,
        ]);
    }
}
