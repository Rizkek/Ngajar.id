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
        Schema::create('materi', function (Blueprint $table) {
            $table->id('materi_id');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->string('judul', 150);
            $table->enum('tipe', ['video', 'pdf', 'soal'])->default('pdf');
            $table->string('file_url')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('kelas_id')
                  ->references('kelas_id')
                  ->on('kelas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi');
    }
};
