<?php

namespace App\Providers;

use App\Http\Responses\PtahVerifyEmailResponse;
use App\Listeners\StartTrialFeedbackFlow;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Lorisleiva\Actions\Facades\Actions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Actions::registerRoutes();
        Actions::registerCommands();

        $this->app->singleton(
            VerifyEmailResponse::class,
            PtahVerifyEmailResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::subscribe(StartTrialFeedbackFlow::class);
    }
}
