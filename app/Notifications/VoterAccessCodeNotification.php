<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoterAccessCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $accessCode,
        protected ?string $name = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your voting access code')
            ->greeting($this->name ? "Hello {$this->name}," : 'Hello,')
            ->line('Your voting access code is below.')
            ->line('Access code: ' . $this->accessCode)
            ->line('Keep this code private and use it to complete your voting verification.');
    }
}