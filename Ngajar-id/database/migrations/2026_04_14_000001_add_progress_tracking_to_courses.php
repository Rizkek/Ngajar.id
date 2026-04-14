<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add progress tracking to kelas_peserta
        Schema::table('kelas_peserta', function (Blueprint $table) {
            $table->integer('progress')->default(0)->after('tanggal_daftar'); // 0-100
            $table->enum('status', ['pending', 'active', 'completed', 'dropped'])->default('active')->after('progress');
            $table->timestamp('completion_date')->nullable()->after('status');
            $table->timestamp('last_accessed_at')->nullable()->after('completion_date');
        });

        // Create material progress tracking table
        Schema::create('material_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('materi_id');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_watched')->default(0); // in seconds
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('materi_id')
                  ->references('materi_id')
                  ->on('materi')
                  ->onDelete('cascade');

            $table->unique(['student_id', 'materi_id']);
            $table->index(['student_id', 'is_completed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_progress');

        Schema::table('kelas_peserta', function (Blueprint $table) {
            $table->dropColumn(['progress', 'status', 'completion_date', 'last_accessed_at']);
        });
    }
};
