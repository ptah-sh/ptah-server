<?php

namespace App\Listeners;

use App\Notifications\AskFinalTrialFeedback;
use App\Notifications\AskMidTrialFeedback;
use Carbon\CarbonInterval;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;

class StartTrialFeedbackFlow
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function subscribe(Dispatcher $dispatcher): array
    {
        return [
            Verified::class => 'scheduleNotifications',
        ];
    }

    public function scheduleNotifications(Verified $event): void
    {
        $team = $event->user->currentTeam;

        $team->createAsCustomer([
            'trial_ends_at' => now()->addDays(14),
        ]);

        if (! $team->onTrial()) {
            return;
        }

        $days7 = $team->trialEndsAt()->sub(CarbonInterval::days(7))->diffAsDateInterval(now(), absolute: true);
        $team->notify((new AskMidTrialFeedback($team))->delay($days7));

        $days1 = $team->trialEndsAt()->sub(CarbonInterval::day())->diffAsDateInterval(now(), absolute: true);
        $team->notify((new AskFinalTrialFeedback($team))->delay($days1));
    }
}
