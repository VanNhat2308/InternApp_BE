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
        Schema::create('sinh_viens', function (Blueprint $table) {
        $table->id('maSV');
        $table->string('tenDangNhap')->unique();
        $table->string('password');
        $table->string('hoTen');
        $table->string('email')->unique();
        $table->string('soDienThoai');
        $table->string('diaChi')->nullable();
        $table->date('ngaySinh')->nullable();
        $table->string('gioiTinh')->nullable();
        $table->string('nganh')->nullable();
        $table->text('duLieuKhuonMat')->nullable();
        $table->string('cV')->nullable();
        $table->string('soDTGV')->nullable();
        $table->string('tenGiangVien')->nullable();
        $table->string('thoiGianTT')->nullable();
        $table->string('viTri')->nullable();

        $table->string('maTruong'); // Foreign key
        $table->foreign('maTruong')->references('maTruong')->on('truongs')->onDelete('cascade');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_viens');
    }
};
