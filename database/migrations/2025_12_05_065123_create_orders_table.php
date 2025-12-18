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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->string('reference_number')->nullable();
            $table->date('event_date')->nullable();
            $table->enum('event_time', ['morning', 'afternoon', 'evening', 'night_snack'])->nullable();
            $table->string('event_menu')->nullable();
            $table->text('address');
            $table->string('order_type')->nullable();
            $table->integer('guest_count')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->timestamps();
            
            $table->index(['tenant_id', 'order_number']);
            $table->index(['tenant_id', 'event_date']);
            $table->index(['tenant_id', 'status']);
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
