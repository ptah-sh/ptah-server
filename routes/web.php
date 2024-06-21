<?php

use App\Http\Controllers\NodeController;
use App\Http\Controllers\NodeTaskGroupController;
use App\Http\Controllers\SwarmTaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('/swarm-tasks/init-cluster', [SwarmTaskController::class, 'initCluster'])->name('swarm-tasks.init-cluster');

    Route::post('/node-task-groups/{taskGroup}/retry', [NodeTaskGroupController::class, 'retry'])->name('node-task-groups.retry');

    Route::resource("nodes", NodeController::class);
});
