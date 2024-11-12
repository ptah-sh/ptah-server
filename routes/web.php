<?php

use App\Actions\Nodes\InitCluster;
use App\Actions\Services\CreateService;
use App\Actions\Services\StartDeployment;
use App\Actions\Teams\Settings\SaveTeamBackupSettings;
use App\Actions\Workers\ExecuteWorker;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\NodeTaskGroupController;
use App\Http\Controllers\RefundPolicyController;
use App\Http\Controllers\ReviewAppsController;
use App\Http\Controllers\ServiceBackupController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SwarmController;
use App\Http\Controllers\SwarmTaskController;
use App\Http\Controllers\TeamBillingController;
use App\Http\Controllers\TeamQuotasController;
use App\Http\Controllers\TeamSettingsController;
use App\Http\Middleware\EnsureTeamSubscription;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->user()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login');
    }
});

Route::get('/refund-policy', [RefundPolicyController::class, 'show'])->name('refund-policy.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/teams-billing', fn () => redirect()->route('teams.billing.show', auth()->user()->currentTeam));
    Route::get('/teams/{team}/billing', [TeamBillingController::class, 'show'])->name('teams.billing.show');
    Route::get('/teams/{team}/quotas', [TeamQuotasController::class, 'index'])->name('teams.billing.quotas');
    Route::get('/teams/{team}/settings', [TeamSettingsController::class, 'index'])->name('teams.settings');
    Route::patch('/teams/{team}/settings/backup', SaveTeamBackupSettings::class)->name('teams.settings.backup');

    Route::patch('/teams/{team}/billing/update-customer', [TeamBillingController::class, 'updateCustomer'])->name('teams.billing.update-customer');
    Route::get('/teams/{team}/billing/download-invoice', [TeamBillingController::class, 'downloadInvoice'])->name('teams.billing.download-invoice');
    Route::get('/teams/{team}/billing/subscription-success', [TeamBillingController::class, 'subscriptionSuccess'])->name('teams.billing.subscription-success');

    Route::middleware([
        EnsureTeamSubscription::class,
    ])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

        Route::post('/swarms/{swarm}/update-docker-registries', [SwarmController::class, 'updateDockerRegistries'])->name('swarms.update-docker-registries');
        Route::post('/swarms/{swarm}/update-s3-storages', [SwarmController::class, 'updateS3Storages'])->name('swarms.update-s3-storages');

        Route::post('/swarm-tasks/init-cluster', InitCluster::class)->name('swarm-tasks.init-cluster');
        Route::post('/swarm-tasks/join-cluster', [SwarmTaskController::class, 'joinCluster'])->name('swarm-tasks.join-cluster');

        Route::post('/node-task-groups/{taskGroup}/retry', [NodeTaskGroupController::class, 'retry'])->name('node-task-groups.retry');

        Route::resource('nodes', NodeController::class);
        Route::get('/nodes/{node}/settings', [NodeController::class, 'settings'])->name('nodes.settings');
        Route::post('/nodes/{node}/upgrade-agent', [NodeController::class, 'upgradeAgent'])->name('nodes.upgrade-agent');

        Route::post('/services/{service:slug}/deployments', StartDeployment::class)->name('services.deploy');
        Route::resource('services', ServiceController::class)->except(['store']);
        Route::post('/services', CreateService::class)->name('services.store');
        Route::get('/services/{service}/deployments', [ServiceController::class, 'deployments'])->name('services.deployments');
        Route::get('/services/{service}/backups', [ServiceBackupController::class, 'index'])->name('services.backups');

        Route::get('/services/{service}/review-apps', [ReviewAppsController::class, 'index'])->name('services.review-apps');

        Route::post('/services/{service}/processes/{process}/workers/{worker}/execute', ExecuteWorker::class)->name('workers.execute');

        Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
            require __DIR__.'/admin.php';
        });
    });
});
