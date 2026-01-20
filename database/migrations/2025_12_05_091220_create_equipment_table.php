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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->onDelete('set null');
            $table->integer('quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->foreignId('equipment_status_id')->nullable()->constrained('equipment_statuses')->onDelete('set null');
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();
            
            $table->index('tenant_id');
            $table->index('equipment_category_id');
            $table->index('equipment_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
