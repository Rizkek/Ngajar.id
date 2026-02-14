<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update Kelas Table
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'kategori')) {
                $table->string('kategori')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('kelas', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('judul');
            }
        });

        // Update Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_beasiswa')) {
                $table->boolean('is_beasiswa')->default(false)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'thumbnail']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_beasiswa');
        });
    }
};
