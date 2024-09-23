<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('owner')
            ->withCount('nodes', 'services', 'onlineNodes', 'deployments')
            ->paginate(30);

        return Inertia::render('Admin/Teams/List', [
            'teams' => $teams,
        ]);
    }

    public function show(Team $team)
    {
        $team->load('owner');
        $quotas = $team->quotas();
        $plan = $team->currentPlan();

        $quotaTypes = array_keys($plan->quotas);
        $quotasArray = [];

        foreach ($quotaTypes as $type) {
            $quotasArray[$type] = [
                'maxUsage' => $quotas->$type->maxUsage,
                'currentUsage' => $quotas->$type->currentUsage(),
                'isSoft' => $plan->quotas[$type]['soft'],
            ];
        }

        return Inertia::render('Admin/Teams/Edit', [
            'team' => $team,
            'quotas' => $quotasArray,
        ]);
    }
}
