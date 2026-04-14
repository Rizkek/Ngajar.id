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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            $table->foreignId('referred_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();
            $table->string('referral_code', 50);
            $table->integer('bonus_token')->default(500); // Bonus tokens for successful referral
            $table->enum('status', ['pending', 'redeemed'])->default('pending');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->index('referrer_id');
            $table->index('referred_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
