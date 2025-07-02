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
        Schema::table('nhat_kies', function (Blueprint $table) {
            // 1. Drop foreign key
            $table->dropForeign(['maSV']);

            // 2. Drop unique index
            $table->dropUnique('nhat_kies_maSV_unique');

            // 3. Thêm lại foreign key (nếu cần, nhưng không cần unique nữa)
            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('nhat_kies', function (Blueprint $table) {
            $table->dropForeign(['maSV']);
            $table->unique('maSV');
            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');
        });
    }
};
