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
        Schema::create('task_comments', function (Blueprint $table) {
             $table->id();
        $table->unsignedBigInteger('task_id');

        // Polymorphic relation (user: Admin or SinhVien)
        $table->unsignedBigInteger('user_id');
        $table->string('user_type'); // "App\Models\Admin" hoặc "App\Models\SinhVien"

        $table->text('noi_dung');
        $table->timestamps();

        $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};
