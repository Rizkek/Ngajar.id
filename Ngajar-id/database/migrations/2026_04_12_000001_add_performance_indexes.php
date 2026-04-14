<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Sprint 1: Add critical indexes for query optimization
     */
    public function up(): void
    {
        try {
            // Index for class queries by status
            Schema::table('kelas', function (Blueprint $table) {
                $table->index('status');
                $table->index('kategori');
                $table->index('pengajar_id');
            });
        } catch (\Exception $e) {
            // Indexes may already exist, skip
        }

        try {
            // Index for user relationship queries
            Schema::table('users', function (Blueprint $table) {
                $table->index('role');
                $table->index('status');
                $table->index('email');
            });
        } catch (\Exception $e) {
            // Indexes may already exist, skip
        }

        try {
            // Index for enrollment lookups
            Schema::table('kelas_peserta', function (Blueprint $table) {
                $table->index(['kelas_id', 'siswa_id']);
                $table->index(['siswa_id', 'kelas_id']);
            });
        } catch (\Exception $e) {
            // Indexes may already exist, skip
        }

        try {
            // Index for material queries
            Schema::table('materi', function (Blueprint $table) {
                $table->index('kelas_id');
                $table->index(['kelas_id', 'created_at']);
            });
        } catch (\Exception $e) {
            // Indexes may already exist, skip
        }

        try {
            // Index for review/assessment
            Schema::table('ulasan_materi', function (Blueprint $table) {
                $table->index(['siswa_id', 'materi_id']);
                $table->index(['materi_id', 'nilai']);
            });
        } catch (\Exception $e) {
            // Indexes may already exist, skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['kategori']);
            $table->dropIndex(['pengajar_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
        });

        Schema::table('kelas_peserta', function (Blueprint $table) {
            $table->dropIndex(['kelas_id', 'siswa_id']);
            $table->dropIndex(['siswa_id', 'kelas_id']);
        });

        Schema::table('materi', function (Blueprint $table) {
            $table->dropIndex(['kelas_id']);
            $table->dropIndex(['kelas_id', 'created_at']);
        });

        Schema::table('ulasan_materi', function (Blueprint $table) {
            $table->dropIndex(['siswa_id', 'materi_id']);
            $table->dropIndex(['materi_id', 'nilai']);
        });
    }
};
