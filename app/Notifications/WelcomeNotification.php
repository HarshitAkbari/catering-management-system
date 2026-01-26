<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $tenantName
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for registering with ' . config('app.name') . '! We\'re excited to have you on board.')
            ->line('Your account has been successfully created for **' . $this->tenantName . '**.')
            ->line('You can now access all the features of our catering management system to streamline your business operations.')
            ->action('Go to Dashboard', route('dashboard'))
            ->line('If you have any questions or need assistance, please don\'t hesitate to reach out to our support team.')
            ->line('Welcome aboard and happy managing!');
    }
}

