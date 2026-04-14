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
        Schema::table('materi', function (Blueprint $table) {
            // Add file storage columns if they don't exist
            if (!Schema::hasColumn('materi', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->after('file_url');
            }
            if (!Schema::hasColumn('materi', 'file_mime_type')) {
                $table->string('file_mime_type')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('materi', 'storage_path')) {
                $table->string('storage_path')->nullable()->after('file_mime_type');
            }
            if (!Schema::hasColumn('materi', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable()->after('storage_path');
            }
            if (!Schema::hasColumn('materi', 'uploaded_at')) {
                $table->timestamp('uploaded_at')->nullable()->after('uploaded_by');
            }
            if (!Schema::hasColumn('materi', 'download_count')) {
                $table->integer('download_count')->default(0)->after('uploaded_at');
            }
            if (!Schema::hasColumn('materi', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('is_premium');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumnIfExists('file_size');
            $table->dropColumnIfExists('file_mime_type');
            $table->dropColumnIfExists('storage_path');
            $table->dropColumnIfExists('uploaded_by');
            $table->dropColumnIfExists('uploaded_at');
            $table->dropColumnIfExists('download_count');
            $table->dropColumnIfExists('is_public');
        });
    }
};
