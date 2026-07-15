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
        Schema::create('ulasan_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ulasan_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['ulasan_id', 'user_id']); // Prevent duplicate votes
            
            // Note: Since 'ulasans' and 'users' may have different PK names, we just rely on application logic or set manual foreign keys if preferred.
            // For now, these index constraints are enough.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan_votes');
    }
};
