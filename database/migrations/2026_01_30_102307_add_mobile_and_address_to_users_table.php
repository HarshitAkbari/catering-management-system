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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('mobile');
        });

        // Add unique constraint on tenant_id and mobile combination
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['tenant_id', 'mobile'], 'users_tenant_mobile_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_tenant_mobile_unique');
            $table->dropColumn(['mobile', 'address']);
        });
    }
};
