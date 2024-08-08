<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function show()
    {
        $team = auth()->user()->currentTeam;

        return Inertia::render('Dashboard', [
            'nodesCount' => $team->nodes()->count(),
            'servicesCount' => $team->services()->count(),
        ]);
    }
}
