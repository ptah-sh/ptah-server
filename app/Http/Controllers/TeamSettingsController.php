<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;

class TeamSettingsController extends Controller
{
    public function index(Team $team)
    {
        return Inertia::render('Teams/Settings', [
            'team' => $team,
        ]);
    }
}
