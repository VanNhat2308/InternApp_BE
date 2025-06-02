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
       Schema::create('ho_sos', function (Blueprint $table) {
        $table->string('maHS')->primary(); // Khóa chính
        $table->unsignedBigInteger('maSV')->unique(); // 1-1: mỗi bản ghi chỉ gắn với 1 SinhVien
        $table->date('ngayNop')->nullable();
        $table->string('trangThai')->nullable();

        $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_sos');
    }
};
