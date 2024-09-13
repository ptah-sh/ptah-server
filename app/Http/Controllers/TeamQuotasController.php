<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;

class TeamQuotasController extends Controller
{
    public function index(Team $team)
    {
        $quotas = $team->quotas();
        $isOnTrial = $team->onTrial();
        $quotaReached = false;

        $formattedQuotas = [];
        foreach ($quotas as $key => $quota) {
            $formattedQuotas[$key] = [
                'currentUsage' => $quota->currentUsage(),
                'maxUsage' => $quota->maxUsage,
                'isSoftQuota' => $quota->isSoftQuota,
                'almostQuotaReached' => $quota->almostQuotaReached(),
                'isIntrinsic' => $key === 'swarms', // Mark swarms as intrinsic
            ];

            if ($quota->quotaReached() && ! $formattedQuotas[$key]['isIntrinsic']) {
                $quotaReached = true;
            }
        }

        return Inertia::render('Teams/Quotas', [
            'team' => $team->only(['id', 'name']),
            'quotas' => $formattedQuotas,
            'isOnTrial' => $isOnTrial,
            'quotaReached' => $quotaReached,
        ]);
    }
}
