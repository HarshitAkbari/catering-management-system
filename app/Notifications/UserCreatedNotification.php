<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $temporaryPassword
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Temporary Password:** ' . $this->temporaryPassword)
            ->action('Login to Your Account', route('login'))
            ->line('Please change your password after your first login for security purposes.')
            ->line('If you did not expect this email, please contact your administrator.');
    }
}

