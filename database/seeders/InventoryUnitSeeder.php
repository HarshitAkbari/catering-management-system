<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\InventoryUnit;
use Illuminate\Database\Seeder;

class InventoryUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'kg',
            'gram',
            'liter',
            'mL',
            'piece',
            'box',
            'packet',
        ];

        foreach ($units as $unitName) {
            InventoryUnit::firstOrCreate(
                [
                    'name' => $unitName,
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

