<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $customer = $this->order->customer;

        return (new MailMessage)
            ->subject('Upcoming Event Reminder - Order #' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that you have an upcoming event scheduled:')
            ->line('**Order Number:** ' . $this->order->order_number)
            ->line('**Customer:** ' . $customer->name)
            ->line('**Event Date:** ' . $this->order->event_date->format('M d, Y'))
            ->line('**Event Time:** ' . ucfirst($this->order->event_time))
            ->line('**Event Type:** ' . ($this->order->order_type ?? 'N/A'))
            ->line('**Guest Count:** ' . ($this->order->guest_count ?? 'N/A'))
            ->line('**Address:** ' . ($this->order->address ?? 'N/A'))
            ->action('View Order', route('orders.show', $this->order))
            ->line('Please ensure all preparations are ready for this event.');
    }
}

