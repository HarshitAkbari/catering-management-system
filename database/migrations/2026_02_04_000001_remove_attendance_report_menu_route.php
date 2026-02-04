<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('menus')
            ->where('name', 'attendance.report')
            ->orWhere('route', 'attendance.report')
            ->delete();
    }

    public function down(): void
    {
        // No-op: the menu entry is obsolete and should not be recreated.
    }
};


