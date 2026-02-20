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
        // 1. Tambah kolom harga_token ke tabel kelas
        if (!Schema::hasColumn('kelas', 'harga_token')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->integer('harga_token')->default(0)->after('status');
            });
        }

        // 2. Perbaiki tabel ulasans (Drop dulu jika ada, lalu buat ulang dengan struktur benar)
        Schema::dropIfExists('ulasans');

        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('ulasan')->nullable();
            $table->timestamps();

            // Satu user hanya boleh review 1x per kelas
            $table->unique(['user_id', 'kelas_id']);
        });

        // 3. Buat tabel diskusi_kelas
        Schema::create('diskusi_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('diskusi_kelas')->cascadeOnDelete(); // Untuk balasan
            $table->text('konten');
            $table->timestamps();
        });

        // 4. Buat tabel catatan_user (Notes)
        Schema::create('catatan_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas', 'kelas_id')->cascadeOnDelete();
            $table->foreignId('materi_id')->nullable()->constrained('materi', 'materi_id')->cascadeOnDelete();
            $table->text('catatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_user');
        Schema::dropIfExists('diskusi_kelas');
        Schema::dropIfExists('ulasans');

        if (Schema::hasColumn('kelas', 'harga_token')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->dropColumn('harga_token');
            });
        }
    }
};
