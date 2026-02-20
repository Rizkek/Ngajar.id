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
            // Drop individual indexes if they exist to avoid redundancy/conflict
            // We use a try-catch or check if exists approach implicitly by just adding the new better one.
            // Postgres can use combined index for (role) queries too if role is the first column.
            
            // Composite index for finding users by role and status (highly used in LandingController)
            $table->index(['role', 'status'], 'users_role_status_index');
        });

        Schema::table('donasi', function (Blueprint $table) {
            // Index for sum('jumlah') aggregation
            $table->index('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_status_index');
        });

        Schema::table('donasi', function (Blueprint $table) {
            $table->dropIndex(['jumlah']);
        });
    }
};
