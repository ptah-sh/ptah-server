<?php

use App\Http\Controllers\NodeController;
use App\Http\Controllers\NodeTaskGroupController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SwarmController;
use App\Http\Controllers\SwarmTaskController;
use App\Http\Controllers\TeamBillingController;
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

    Route::post('/swarms/{swarm}/update-docker-registries', [SwarmController::class, 'updateDockerRegistries'])->name('swarms.update-docker-registries');
    Route::post('/swarms/{swarm}/update-s3-storages', [SwarmController::class, 'updateS3Storages'])->name('swarms.update-s3-storages');

    Route::post('/swarm-tasks/init-cluster', [SwarmTaskController::class, 'initCluster'])->name('swarm-tasks.init-cluster');
    Route::post('/swarm-tasks/join-cluster', [SwarmTaskController::class, 'joinCluster'])->name('swarm-tasks.join-cluster');

    Route::post('/node-task-groups/{taskGroup}/retry', [NodeTaskGroupController::class, 'retry'])->name('node-task-groups.retry');

    Route::resource('nodes', NodeController::class);
    Route::post('/nodes/{node}/upgrade-agent', [NodeController::class, 'upgradeAgent'])->name('nodes.upgrade-agent');

    Route::resource('services', ServiceController::class);
    Route::get('/services/{service}/deployments', [ServiceController::class, 'deployments'])->name('services.deployments');
    Route::post('/services/{service}/deployments', [ServiceController::class, 'deploy'])->name('services.deploy');

    Route::get('/teams/{team}/billing', [TeamBillingController::class, 'show'])->name('teams.billing.show');
    Route::patch('/teams/{team}/billing/update-customer', [TeamBillingController::class, 'updateCustomer'])->name('teams.billing.update-customer');
    Route::get('/teams/{team}/billing/download-invoice', [TeamBillingController::class, 'downloadInvoice'])->name('teams.billing.download-invoice');
});
