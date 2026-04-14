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
        Schema::table('kelas', function (Blueprint $table) {
            // Prerequisites & Level Requirements
            $table->unsignedBigInteger('prerequisite_kelas_id')->nullable()->after('kategori');
            $table->integer('min_level')->default(1)->after('prerequisite_kelas_id');
            $table->integer('min_xp')->default(0)->after('min_level');
            $table->integer('min_age')->nullable()->after('min_xp');

            // Capacity & Enrollment Deadline
            $table->integer('max_students')->nullable()->after('min_age');
            $table->timestamp('enrollment_deadline')->nullable()->after('max_students');

            // Foreign key
            $table->foreign('prerequisite_kelas_id')
                ->references('kelas_id')
                ->on('kelas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_kelas_id']);
            $table->dropColumn(['prerequisite_kelas_id', 'min_level', 'min_xp', 'min_age', 'max_students', 'enrollment_deadline']);
        });
    }
};
