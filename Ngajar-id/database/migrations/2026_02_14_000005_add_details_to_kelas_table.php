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
            // kategori and thumbnail already added in 2026_02_14_000001
            $table->string('level')->default('Beginner')->after('thumbnail'); // Beginner, Intermediate, Advanced
            $table->integer('harga')->default(0)->after('level'); // Harga token (0 = gratis)
            $table->decimal('rating', 3, 2)->default(0.00)->after('harga'); // 0.00 - 5.00
            $table->integer('total_siswa')->default(0)->after('rating');
            $table->string('durasi')->nullable()->after('total_siswa'); // e.g. "2 Jam 30 Menit"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['level', 'harga', 'rating', 'total_siswa', 'durasi']);
        });
    }
};
