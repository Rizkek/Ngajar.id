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
        try {
            Schema::table('token_log', function (Blueprint $table) {
                $table->index(['user_id', 'tipe']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('donasi', function (Blueprint $table) {
                $table->index('tanggal');
                $table->index(['status', 'tanggal']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_id', 'read_at']);
                $table->index(['notifiable_id', 'created_at']);
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_log', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'tipe']);
        });

        Schema::table('donasi', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['status', 'tanggal']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_id', 'read_at']);
            $table->dropIndex(['notifiable_id', 'created_at']);
        });
    }
};
