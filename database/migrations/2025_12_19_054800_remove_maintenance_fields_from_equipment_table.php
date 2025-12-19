<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing records with 'maintenance' status to 'available'
        DB::table('equipment')
            ->where('status', 'maintenance')
            ->update(['status' => 'available']);

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['last_maintenance_date', 'next_maintenance_date']);
        });

        // Modify the enum to remove 'maintenance' option
        // MySQL requires raw SQL to modify enum columns
        DB::statement("ALTER TABLE equipment MODIFY COLUMN status ENUM('available', 'damaged') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->date('last_maintenance_date')->nullable()->after('status');
            $table->date('next_maintenance_date')->nullable()->after('last_maintenance_date');
        });

        // Restore the enum to include 'maintenance'
        DB::statement("ALTER TABLE equipment MODIFY COLUMN status ENUM('available', 'maintenance', 'damaged') DEFAULT 'available'");
    }
};
