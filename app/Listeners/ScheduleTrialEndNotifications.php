<?php

namespace App\Listeners;

use App\Models\Team;
use App\Notifications\TrialEndsSoonNotification;
use Carbon\CarbonInterval;
use Illuminate\Events\Dispatcher;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Events\SubscriptionUpdated;

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
            SubscriptionCreated::class => 'scheduleNotifications',
            SubscriptionUpdated::class => 'scheduleNotifications',
        ];
    }

    public function scheduleNotifications(SubscriptionCreated|SubscriptionUpdated $event): void
    {
        if (! $event->subscription->trial_ends_at) {
            return;
        }

        $subscription = $event->subscription;

        /* @var Team $team */
        $team = $subscription->billable;

        $days3 = $subscription->trial_ends_at->sub(CarbonInterval::days(3))->diffAsDateInterval(now(), absolute: true);
        $team->notify((new TrialEndsSoonNotification($team))->delay($days3));

        $days1 = $subscription->trial_ends_at->sub(CarbonInterval::day())->diffAsDateInterval(now(), absolute: true);
        $team->notify((new TrialEndsSoonNotification($team))->delay($days1));
    }
}
