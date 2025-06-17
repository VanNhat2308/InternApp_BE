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
        Schema::table('ho_sos', function (Blueprint $table) {
            // Xóa foreign key cũ
            $table->dropForeign(['maSV']);

            // Thêm foreign key mới với cascade
            $table->foreign('maSV')
                  ->references('maSV')
                  ->on('sinh_viens')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ho_sos', function (Blueprint $table) {
            // Xóa foreign key vừa thêm
            $table->dropForeign(['maSV']);

            // Thêm lại foreign key như cũ (nếu bạn biết định nghĩa cũ, thêm vào đây)
            $table->foreign('maSV')
                  ->references('maSV')
                  ->on('sinh_viens');
        });
    }
};
