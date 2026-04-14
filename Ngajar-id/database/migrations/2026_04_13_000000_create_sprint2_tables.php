<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Live class sessions
        if (!Schema::hasTable('live_sessions')) {
            Schema::create('live_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('class_id');
                $table->unsignedInteger('instructor_id');
                $table->string('room');
                $table->string('url');
                $table->string('status')->default('active'); // active, ended
                $table->timestamp('started_at');
                $table->timestamp('expires_at');
                $table->timestamp('ended_at')->nullable();
                $table->timestamps();
                $table->index(['class_id', 'status']);
            });
        }

        // Live session attendance
        if (!Schema::hasTable('live_attendance')) {
            Schema::create('live_attendance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id');
                $table->unsignedInteger('user_id');
                $table->timestamp('joined_at');
                $table->timestamp('left_at')->nullable();
                $table->timestamps();
                $table->foreign('session_id')->references('id')->on('live_sessions')->cascadeOnDelete();
                $table->index(['session_id', 'user_id']);
            });
        }

        // Course progress tracking (may already exist from earlier migrations)
        if (!Schema::hasTable('user_path_progress')) {
            Schema::create('user_path_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('path_id');
                $table->float('progress_percentage')->default(0);
                $table->unsignedInteger('current_kelas_id')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'path_id']);
                $table->index(['user_id', 'completed_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_path_progress');
        Schema::dropIfExists('live_attendance');
        Schema::dropIfExists('live_sessions');
    }
};
