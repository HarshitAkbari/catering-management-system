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
            $table->string('order_number');
            $table->date('event_date')->nullable();
            $table->foreignId('event_time_id')->nullable()->constrained('event_times')->onDelete('set null');
            $table->string('event_menu')->nullable();
            $table->foreignId('order_type_id')->nullable()->constrained('order_types')->onDelete('set null');
            $table->text('address');
            $table->integer('guest_count')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->foreignId('order_status_id')->nullable()->constrained('order_statuses')->onDelete('set null');
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->timestamps();
            
            $table->index(['tenant_id', 'order_number']);
            $table->index(['tenant_id', 'event_date']);
            $table->index('order_status_id');
            $table->index('event_time_id');
            $table->index('order_type_id');
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
