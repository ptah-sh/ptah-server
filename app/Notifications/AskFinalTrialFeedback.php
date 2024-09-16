<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AskFinalTrialFeedback extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Team $team
    ) {
        $this->afterCommit();
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $this->team->onTrial();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $trialEndsAt = $this->team->trialEndsAt();
        $dateDiff = $trialEndsAt->longRelativeToNowDiffForHumans();

        return (new MailMessage)
            ->subject("Your Ptah.sh trial ends soon - We'd love your final feedback")
            ->greeting("Hello {$this->team->owner->name}!")
            ->line("Your Ptah.sh trial for team {$this->team->name} is ending soon.")
            ->line("You will not be able to use Ptah.sh after {$trialEndsAt->toDateTimeString()} ({$dateDiff}).")
            ->line('Before your trial ends, we\'d greatly appreciate your final thoughts.')
            ->line('Your feedback is crucial in helping us improve Ptah.sh for you and future users.')
            ->line('Please reply to this email with your experience and any suggestions you might have.')
            ->line('Thank you for trying Ptah.sh. We hope you\'ve found it valuable!')
            ->line('If you\'re interested in continuing with Ptah.sh, please visit our pricing page to choose a plan that suits your needs.')
            ->action('Choose a Plan', route('teams.billing.show', $this->team))
            ->line('If you have any questions or need assistance, don\'t hesitate to reach out at '.config('app.email').'.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
