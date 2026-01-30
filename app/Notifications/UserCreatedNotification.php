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
        $role = ucfirst($notifiable->role ?? 'User');
        $roleMessage = match(strtolower($notifiable->role ?? '')) {
            'admin' => 'You have been assigned the **Admin** role with full system access.',
            'manager' => 'You have been assigned the **Manager** role with management access.',
            'staff' => 'You have been assigned the **Staff** role with limited access.',
            default => 'Your account has been created successfully.',
        };

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Your Account Has Been Created')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your account has been created successfully.')
            ->line($roleMessage)
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Temporary Password:** ' . $this->temporaryPassword)
            ->line('**Role:** ' . $role)
            ->action('Login to Your Account', route('login'))
            ->line('**Important:** Please change your password after your first login for security purposes.')
            ->line('If you did not expect this email, please contact your administrator immediately.');
    }
}

