<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AskMidTrialFeedback extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Team $team
    ) {
        $this->afterCommit();
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
        $nodeCount = $this->team->nodes()->count();
        $serviceCount = $this->team->services()->count();

        $message = (new MailMessage)
            ->subject('We\'d love your feedback on Ptah.sh')
            ->greeting("Hello {$this->team->owner->name}!")
            ->line('We hope you\'re enjoying your experience with Ptah.sh so far.')
            ->line('We\'d love to hear your thoughts and improve our service based on your feedback.')
            ->line('Simply reply to this email with your feedback - we\'re eager to hear from you!');

        if ($nodeCount === 0) {
            $message->line('We noticed you don\'t have any active nodes at the moment. We\'d love to know if there\'s anything holding you back or if you need any assistance getting started or restarting with nodes.');
        } elseif ($serviceCount === 1) {
            $message->line('We see you haven\'t created any custom services yet. How has your experience been so far? Is there anything we can do to make it easier for you to create and manage services?');
        } elseif ($serviceCount === 2) {
            $message->line('We see you\'ve created your first custom service. How was your experience? Is there anything we can do to make it easier for you to manage or create more services?');
        } else {
            $message->line('It looks like you\'re actively using Ptah.sh with multiple custom services. We\'d love to hear about your experience and any suggestions you might have for improvements.');
        }

        return $message
            ->line('Your feedback is invaluable to us and will help shape the future of Ptah.sh.')
            ->line('Also, we invite you to join our Discord community to connect with other users and our team.')
            ->action('Join Our Discord', 'https://r.ptah.sh/chat')
            ->line('Thank you for being a part of Ptah.sh!');
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
