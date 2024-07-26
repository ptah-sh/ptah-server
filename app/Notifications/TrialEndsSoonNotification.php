<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEndsSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Team $team,
    )
    {
        $this->afterCommit();
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $this->team->onTrial();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $nextPayment = $this->team->subscription()->nextPayment();

        return (new MailMessage)
                    ->line('Your trial ends soon.')
                    ->line("You will be charged {$nextPayment->amount()} on {$nextPayment->date->toDateTimeString()} ({$nextPayment->date->longRelativeToNowDiffForHumans()}).")
                    ->action('Manage Subscription', url(route('teams.billing.show', $this->team)))
                    ->line('Any other questions? Contact us at '.config('app.email'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
