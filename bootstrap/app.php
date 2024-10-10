<?php

use ApiNodes\Http\Middleware\AgentTokenAuth;
use App\Console\Commands\ExecuteScheduledWorker;
use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\HandleInertiaRequests;
use App\Jobs\CheckAgentUpdates;
use App\Models\Scopes\TeamScope;
use App\Models\Service;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'paddle/*',
        ]);

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
                'admin' => AdminAccess::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })
    ->withSchedule(function (Schedule $schedule) {
        if (! app()->runningConsoleCommand('schedule:run', 'schedule:work')) {
            return;
        }

        if (app()->runningConsoleCommand('schedule:work') && config('app.env') === 'production') {
            $schedule->command('schedule:run')
                ->everyMinute()
                ->onOneServer()
                ->withoutOverlapping();

            return;
        }

        $schedule->job(CheckAgentUpdates::class)
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping();

        Service::withoutGlobalScope(TeamScope::class)->with(['latestDeployment'])->chunk(100, function (
            /* @var Service[] $services */
            $services
        ) use ($schedule) {
            foreach ($services as $service) {
                foreach ($service->latestDeployment->data->processes as $process) {
                    foreach ($process->workers as $worker) {
                        if (! $worker->crontab) {
                            continue;
                        }

                        $schedule
                            ->command(ExecuteScheduledWorker::class, [
                                '--service-id' => $service->id,
                                '--process' => $process->dockerName,
                                '--worker' => $worker->dockerName,
                            ])
                            ->cron($worker->crontab)
                            ->onOneServer()
                            ->withoutOverlapping();
                    }
                }
            }
        });
    })
    ->create();
