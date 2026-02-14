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
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->boolean('is_free')->default(true)->after('is_active');
        });

        // Auto-set: Beginner paths = FREE by default
        DB::statement("UPDATE learning_paths SET is_free = true WHERE level = 'Beginner'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
};
