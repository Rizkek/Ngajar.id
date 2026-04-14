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
            // Add phone number field (for pengajar mainly)
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }

            // Add referral code generation
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code', 50)->unique()->nullable()->after('phone');
            }

            // Add avatar path
            if (!Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('avatar');
            }

            // Add email notifications preference
            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('email_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('phone');
            $table->dropColumnIfExists('referral_code');
            $table->dropColumnIfExists('avatar_path');
            $table->dropColumnIfExists('email_notifications');
        });
    }
};
