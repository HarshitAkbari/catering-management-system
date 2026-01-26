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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->index();
            $table->string('route_name')->nullable();
            $table->text('url');
            $table->string('http_method', 10);
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamp('visited_at')->index();
            $table->timestamps();

            $table->index(['user_id', 'visited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
