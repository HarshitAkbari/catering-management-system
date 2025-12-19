<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop unique constraint on order_number (multiple orders can share same order_number)
            $table->dropUnique(['order_number']);
            // Drop reference_number index and column
            $table->dropIndex(['reference_number']);
            $table->dropColumn('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Restore unique constraint on order_number
            $table->unique('order_number');
            // Restore reference_number column
            $table->string('reference_number')->nullable()->after('order_number');
            $table->index('reference_number');
        });
    }
};
