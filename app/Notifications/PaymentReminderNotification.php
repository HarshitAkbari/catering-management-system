<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
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
        $amount = $this->order->estimated_cost;
        $paidAmount = $this->order->invoice?->payments->sum('amount') ?? 0;
        $pendingAmount = $amount - $paidAmount;

        return (new MailMessage)
            ->subject('Payment Reminder - Order #' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that there is a pending payment for Order #' . $this->order->order_number . '.')
            ->line('**Customer:** ' . $customer->name)
            ->line('**Event Date:** ' . $this->order->event_date->format('M d, Y'))
            ->line('**Total Amount:** ₹' . number_format($amount, 2))
            ->line('**Paid Amount:** ₹' . number_format($paidAmount, 2))
            ->line('**Pending Amount:** ₹' . number_format($pendingAmount, 2))
            ->action('View Order', route('orders.show', $this->order))
            ->line('Thank you for using our catering management system!');
    }
}

