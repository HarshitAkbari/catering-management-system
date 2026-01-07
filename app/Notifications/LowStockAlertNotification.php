<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public InventoryItem $item
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert - ' . $this->item->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is an alert that the following inventory item is running low on stock:')
            ->line('**Item:** ' . $this->item->name)
            ->line('**Current Stock:** ' . $this->item->current_stock . ' ' . ($this->item->unit ?? ''))
            ->line('**Minimum Stock:** ' . $this->item->minimum_stock . ' ' . ($this->item->unit ?? ''))
            ->action('View Inventory', route('inventory.show', $this->item))
            ->line('Please restock this item soon to avoid running out.');
    }
}

