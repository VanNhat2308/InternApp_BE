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
     Schema::create('admin', function (Blueprint $table) {
        $table->id('maAdmin');   // Khóa chính
        $table->string('password');
        $table->string('email')->unique();
        $table->string('hoTen');
        $table->timestamps(); // Tạo 2 cột: created_at & updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
