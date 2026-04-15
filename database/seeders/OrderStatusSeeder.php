<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pending',
            'Confirmed',
            'In Progress',
            'Completed',
            'Cancelled',
        ];

        foreach ($statuses as $statusName) {
            OrderStatus::firstOrCreate(
                [
                    'name' => $statusName,
                    'tenant_id' => null,
                ],
                [
                    'is_system' => 1,
                    'is_active' => 1,
                ]
            );
        }
    }
}

