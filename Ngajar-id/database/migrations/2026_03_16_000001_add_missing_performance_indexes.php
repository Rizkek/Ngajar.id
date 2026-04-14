<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->index('pengajar_id');
        });

        Schema::table('ulasans', function (Blueprint $table) {
            $table->index('kelas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ulasans', function (Blueprint $table) {
            $table->dropIndex(['kelas_id']);
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex(['pengajar_id']);
        });
    }
};
