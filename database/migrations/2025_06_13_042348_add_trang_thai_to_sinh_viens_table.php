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
    Schema::table('sinh_viens', function (Blueprint $table) {
        $table->enum('trangThai', ['Đang thực tập', 'Chưa thực tập','Đã nghỉ'])->default('Đang thực tập')->after('maTruong');
    });
}

public function down(): void
{
    Schema::table('sinh_viens', function (Blueprint $table) {
        $table->dropColumn('trangThai');
    });
}



};
