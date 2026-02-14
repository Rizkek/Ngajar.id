<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('deskripsi');
            $table->integer('harga_token')->default(0)->after('is_premium');
        });

        Schema::create('materi_akses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('materi_id');
            $table->timestamp('unlocked_at')->useCurrent();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('materi_id')->references('materi_id')->on('materi')->onDelete('cascade');

            // Prevent duplicate unlock
            $table->unique(['user_id', 'materi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_akses');

        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'harga_token']);
        });
    }
};
