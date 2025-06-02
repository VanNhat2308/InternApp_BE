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
        Schema::create('bao_caos', function (Blueprint $table) {
            $table->string('maBC')->primary();       // Khóa chính
            $table->string('loai')->nullable();
            $table->date('ngayTao')->nullable();
            $table->text('noiDung')->nullable();

            $table->unsignedBigInteger('maSV')->unique(); // 1-1: mỗi bản ghi chỉ gắn với 1 SinhVien     // Khóa ngoại, unique để đảm bảo 1-1
            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bao_caos');
    }
};
