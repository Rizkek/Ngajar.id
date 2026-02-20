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
        if (!Schema::hasColumn('learning_paths', 'harga_token')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->integer('harga_token')->default(0)->after('is_free');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('learning_paths', 'harga_token')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->dropColumn('harga_token');
            });
        }
    }
};
