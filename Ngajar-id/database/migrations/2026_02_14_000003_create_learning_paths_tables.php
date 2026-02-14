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
        // Tabel Learning Paths
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id('path_id');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 100)->nullable(); // Programming, Design, Business, etc
            $table->enum('level', ['Beginner', 'Intermediate', 'Advanced'])->default('Beginner');
            $table->integer('estimated_hours')->default(0); // Total jam belajar estimasi
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // Pengajar yang buat path
            $table->boolean('is_active')->default(true);
            $table->integer('total_enrolled')->default(0); // Cache untuk jumlah yang enroll
            $table->timestamps();

            $table->foreign('created_by')->references('user_id')->on('users')->nullOnDelete();
        });

        // Pivot table: Path <-> Kelas (Many to Many)
        Schema::create('learning_path_kelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('path_id');
            $table->unsignedBigInteger('kelas_id');
            $table->integer('urutan')->default(1); // Urutan kelas dalam path
            $table->boolean('is_required')->default(true); // Wajib atau optional
            $table->timestamps();

            $table->foreign('path_id')->references('path_id')->on('learning_paths')->cascadeOnDelete();
            $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->cascadeOnDelete();

            // Unique constraint: satu kelas tidak bisa muncul 2x dalam 1 path
            $table->unique(['path_id', 'kelas_id']);
        });

        // Tabel Progress User di Learning Path
        Schema::create('user_path_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('path_id');
            $table->unsignedBigInteger('current_kelas_id')->nullable(); // Kelas yang sedang diambil
            $table->json('completed_kelas')->nullable(); // Array kelas_id yang sudah selesai
            $table->integer('progress_percentage')->default(0); // 0-100
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('path_id')->references('path_id')->on('learning_paths')->cascadeOnDelete();
            $table->foreign('current_kelas_id')->references('kelas_id')->on('kelas')->nullOnDelete();

            // User hanya bisa enroll 1x per path
            $table->unique(['user_id', 'path_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_path_progress');
        Schema::dropIfExists('learning_path_kelas');
        Schema::dropIfExists('learning_paths');
    }
};
