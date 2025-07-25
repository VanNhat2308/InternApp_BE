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
            $table->string('tieuDe');
            $table->text('noiDung');
            $table->date('hanHoanThanh')->nullable();
            $table->string('trangThai')->default('Chưa hoàn thành');
            $table->string('nguoiGiao')->nullable();
            $table->timestamps();
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
