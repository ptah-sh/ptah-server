<?php

use ApiNodes\Http\Middleware\AgentTokenAuth;
use App\Console\Commands\DispatchBackupTask;
use App\Http\Middleware\HandleInertiaRequests;
use App\Jobs\CheckAgentUpdates;
use App\Models\DeploymentData\CronPreset;
use App\Models\Service;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies('*');

        $middleware
            ->api(prepend: [
                AgentTokenAuth::class,
            ])
            ->web(append: [
                HandleInertiaRequests::class,
                AddLinkHeadersForPreloadedAssets::class,
            ])
            ->alias([
                'abilities' => CheckAbilities::class,
                'ability' => CheckForAnyAbility::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(CheckAgentUpdates::class)
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping();

        Service::withoutGlobalScopes()->with(['latestDeployment' => fn ($query) => $query->withoutGlobalScopes()])->chunk(100, function (
            /* @var Service[] $services */
            $services
        ) use ($schedule) {
            foreach ($services as $service) {
                foreach ($service->latestDeployment->data->processes as $process) {
                    if ($process->replicas === 0) {
                        continue;
                    }
                    foreach ($process->volumes as $volume) {
                        $backupSchedule = $volume->backupSchedule;
                        if ($backupSchedule === null) {
                            continue;
                        }

                        $schedule
                            ->command(DispatchBackupTask::class, [
                                'serviceId' => $service->id,
                                'volumeId' => $volume->id,
                            ])
                            ->cron($backupSchedule->expr)
                            ->onOneServer()
                            ->withoutOverlapping();
                    }
                }
            }
        });
    })
    ->create();
