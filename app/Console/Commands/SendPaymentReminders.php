<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature = 'notifications:payment-reminders';

    protected $description = 'Send payment reminder notifications for orders with pending or partial payments';

    public function handle(): int
    {
        $this->info('Sending payment reminders...');

        $orders = Order::whereIn('payment_status', ['pending', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->with('customer', 'invoice.payments')
            ->get();

        $sentCount = 0;

        foreach ($orders as $order) {
            // Get all users for the tenant
            $users = User::where('tenant_id', $order->tenant_id)
                ->where('status', 'active')
                ->get();

            foreach ($users as $user) {
                $user->notify(new PaymentReminderNotification($order));
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} payment reminder notifications.");

        return Command::SUCCESS;
    }
}

