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
         Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maSV');
            $table->string('tieuDe');
            $table->text('noiDung');
            $table->date('hanHoanThanh')->nullable();
            $table->string('trangThai')->default('Chưa hoàn thành');
            $table->timestamps();

            $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
