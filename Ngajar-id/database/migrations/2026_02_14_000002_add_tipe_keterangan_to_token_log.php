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
        if (!Schema::hasColumn('token_log', 'tipe')) {
            Schema::table('token_log', function (Blueprint $table) {
                $table->string('tipe', 50)->nullable()->after('aksi'); // 'penggunaan', 'pendapatan', 'komisi', 'topup'
            });
        }

        if (!Schema::hasColumn('token_log', 'keterangan')) {
            Schema::table('token_log', function (Blueprint $table) {
                $table->text('keterangan')->nullable()->after('tipe');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('token_log', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'keterangan']);
        });
    }
};
