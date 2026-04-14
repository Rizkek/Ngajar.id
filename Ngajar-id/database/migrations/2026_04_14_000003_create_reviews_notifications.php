<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notifications table
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('type'); // 'course_update', 'progress', 'assignment', 'message'
                $table->string('title');
                $table->text('message');
                $table->string('action_url')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'is_read']);
            });
        }

        // Course reviews/ratings table
        if (!Schema::hasTable('ulasans')) {
            Schema::create('ulasans', function (Blueprint $table) {
                $table->id('ulasan_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('kelas_id');
                $table->integer('rating'); // 1-5
                $table->text('komentar');
                $table->integer('helpful_count')->default(0);
                $table->boolean('is_verified_purchase')->default(false);
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('kelas_id')->references('kelas_id')->on('kelas')->onDelete('cascade');
                $table->unique(['user_id', 'kelas_id']);
                $table->index(['kelas_id', 'rating']);
            });
        }

        // Material feedback table
        if (!Schema::hasTable('materi_feedback')) {
            Schema::create('materi_feedback', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('materi_id');
                $table->integer('rating'); // 1-5
                $table->text('feedback')->nullable();
                $table->boolean('is_helpful')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('materi_id')->references('materi_id')->on('materi')->onDelete('cascade');
                $table->unique(['user_id', 'materi_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_feedback');
        Schema::dropIfExists('ulasans');
        Schema::dropIfExists('notifications');
    }
};
