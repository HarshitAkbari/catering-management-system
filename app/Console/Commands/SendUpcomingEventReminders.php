<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use App\Notifications\UpcomingEventNotification;
use Illuminate\Console\Command;

class SendUpcomingEventReminders extends Command
{
    protected $signature = 'notifications:upcoming-events';

    protected $description = 'Send upcoming event reminder notifications (24 hours before event)';

    public function handle(): int
    {
        $this->info('Sending upcoming event reminders...');

        // Get orders with events happening in the next 24 hours
        $tomorrow = now()->addDay()->startOfDay();
        $dayAfter = now()->addDays(2)->startOfDay();

        // Get all tenants and their confirmed status IDs
        $tenants = \App\Models\Tenant::all();
        $orders = collect();
        
        foreach ($tenants as $tenant) {
            $confirmedStatusId = OrderStatus::where('tenant_id', $tenant->id)->where('name', 'confirmed')->value('id');
            
            if ($confirmedStatusId) {
                $tenantOrders = Order::where('tenant_id', $tenant->id)
                    ->where('event_date', '>=', $tomorrow)
                    ->where('event_date', '<', $dayAfter)
                    ->where('order_status_id', $confirmedStatusId)
                    ->with('customer')
                    ->get();
                    
                $orders = $orders->merge($tenantOrders);
            }
        }

        $sentCount = 0;

        foreach ($orders as $order) {
            // Get all users for the tenant
            $users = User::where('tenant_id', $order->tenant_id)
                ->where('status', 'active')
                ->get();

            foreach ($users as $user) {
                $user->notify(new UpcomingEventNotification($order));
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} upcoming event reminder notifications.");

        return Command::SUCCESS;
    }
}

