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
        Schema::create('diem_danhs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maSV');
            $table->date('ngay_diem_danh');
            $table->time('gio_bat_dau')->nullable();
            $table->time('gio_ket_thuc')->nullable();
            $table->enum('trang_thai', ['co_mat', 'vang', 'muon'])->default('co_mat');
            $table->string('ghi_chu')->nullable();
            $table->timestamps();

            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diem_danhs');
    }
};
