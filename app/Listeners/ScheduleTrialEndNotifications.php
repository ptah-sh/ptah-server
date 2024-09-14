<?php

namespace App\Listeners;

use App\Notifications\TrialEndsSoonNotification;
use Carbon\CarbonInterval;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;

class ScheduleTrialEndNotifications
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

        $days3 = $team->trialEndsAt()->sub(CarbonInterval::days(3))->diffAsDateInterval(now(), absolute: true);
        $team->notify((new TrialEndsSoonNotification($team))->delay($days3));

        $days1 = $team->trialEndsAt()->sub(CarbonInterval::day())->diffAsDateInterval(now(), absolute: true);
        $team->notify((new TrialEndsSoonNotification($team))->delay($days1));
    }
}
