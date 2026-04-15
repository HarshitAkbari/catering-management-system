<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EventTime;
use Illuminate\Database\Seeder;

class EventTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTimes = [
            'Morning',
            'Afternoon',
            'Evening',
            'Night',
        ];

        foreach ($eventTimes as $eventTimeName) {
            EventTime::firstOrCreate(
                [
                    'name' => $eventTimeName,
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

