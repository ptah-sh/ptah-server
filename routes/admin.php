<?php

use App\Actions\Admin\Teams\OverrideTeamQuotas;
use App\Http\Controllers\Admin\TeamController;

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
Route::put('/teams/{team}', OverrideTeamQuotas::class)->name('teams.update');
