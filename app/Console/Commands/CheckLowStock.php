<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\LowStockAlertNotification;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'notifications:low-stock';

    protected $description = 'Send low stock alert notifications for inventory items below minimum stock';

    public function handle(): int
    {
        $this->info('Checking for low stock items...');

        $lowStockItems = InventoryItem::whereRaw('current_stock <= minimum_stock')
            ->get();

        $sentCount = 0;

        foreach ($lowStockItems as $item) {
            // Get all users for the tenant
            $users = User::where('tenant_id', $item->tenant_id)
                ->where('status', 'active')
                ->get();

            foreach ($users as $user) {
                $user->notify(new LowStockAlertNotification($item));
                $sentCount++;
            }
        }

        $this->info("Sent {$sentCount} low stock alert notifications.");

        return Command::SUCCESS;
    }
}

