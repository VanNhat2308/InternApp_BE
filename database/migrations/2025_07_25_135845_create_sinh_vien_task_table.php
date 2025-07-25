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
       Schema::create('sinh_vien_task', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('maSV');
    $table->unsignedBigInteger('task_id');
    $table->timestamps();

    $table->foreign('maSV')->references('maSV')->on('sinh_viens')->onDelete('cascade');
    $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_vien_task');
    }
};
