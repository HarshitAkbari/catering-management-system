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
            ['full_name' => 'Kilogram', 'short_name' => 'kg'],
            ['full_name' => 'Gram', 'short_name' => 'g'],
            ['full_name' => 'Liter', 'short_name' => 'L'],
            ['full_name' => 'Milliliter', 'short_name' => 'mL'],
            ['full_name' => 'Piece', 'short_name' => 'pc'],
            ['full_name' => 'Box', 'short_name' => 'box'],
            ['full_name' => 'Packet', 'short_name' => 'pkt'],
        ];

        foreach ($units as $unit) {
            InventoryUnit::firstOrCreate(
                [
                    'full_name' => $unit['full_name'],
                    'tenant_id' => null,
                ],
                [
                    'short_name' => $unit['short_name'],
                    'is_system' => 1,
                    'is_active' => 1,
                    'created_by' => null,
                ]
            );
        }
    }
}

