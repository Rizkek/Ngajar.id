<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Leaderboard/Rankings table
        if (!Schema::hasTable('leaderboards')) {
            Schema::create('leaderboards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->integer('xp')->default(0);
                $table->integer('level')->default(1);
                $table->integer('courses_completed')->default(0);
                $table->integer('total_points')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->unique('user_id');
                $table->index(['xp', 'level']);
            });
        }

        // Achievement badges table
        if (!Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('description');
                $table->string('icon_url')->nullable();
                $table->string('badge_color')->default('#4CAF50');
                $table->integer('points')->default(10);
                $table->timestamps();
            });
        }

        // User achievements (pivot)
        if (!Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('achievement_id');
                $table->timestamp('earned_at');
                $table->timestamps();

                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
                $table->unique(['user_id', 'achievement_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('leaderboards');
    }
};
