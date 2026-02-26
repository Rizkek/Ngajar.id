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
        Schema::create('broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('recipient_type');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->string('action_url')->nullable();
            $table->string('priority');
            $table->integer('recipient_count')->default(0);
            $table->unsignedBigInteger('sent_by');
            $table->timestamps();

            $table->foreign('sent_by')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_logs');
    }
};
