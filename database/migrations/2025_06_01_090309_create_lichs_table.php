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
        Schema::create('lichs', function (Blueprint $table) {
            $table->string('maLich')->primary();      // Khóa chính
            $table->date('ngay');                     // Ngày diễn ra lịch
            $table->text('noiDung')->nullable();      // Nội dung
            $table->string('trangThai')->nullable();  // Trạng thái (VD: đã học, nghỉ...)

            $table->unsignedBigInteger('maSV'); // 1-1: mỗi bản ghi chỉ gắn với 1 SinhVien
            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lichs');
    }
};
