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
        Schema::create('donasi', function (Blueprint $table) {
            $table->id('donasi_id');
            $table->string('nama', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->integer('jumlah')->default(0);
            $table->timestamp('tanggal')->nullable();
            $table->text('pesan')->nullable();
            $table->string('status', 20)->default('pending'); // pending, paid, failed
            $table->string('metode_pembayaran', 50)->nullable(); // bank, ewallet, qris
            $table->string('nomor_transaksi', 50)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};
