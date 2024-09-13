<?php

namespace App\Providers;

use App\Listeners\ScheduleTrialEndNotifications;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Lorisleiva\Actions\Facades\Actions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Actions::registerRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::subscribe(ScheduleTrialEndNotifications::class);
    }
}
