<?php

namespace App\Listeners;

use App\Models\Team;
use Illuminate\Support\Str;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener
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
    public function handle(WebhookReceived $event): void
    {
        if (Str::startsWith($event->payload['event_type'], 'subscription.')) {
            $customData = $event->payload['data']['custom_data'];

            if (isset($customData['team_id'])) {
                Team::whereId($customData['team_id'])->update([
                    'activating_subscription' => false,
                ]);
            }
        }
    }
}
