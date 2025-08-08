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
           Schema::create('notifications', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('notifiable_id'); // id của người nhận
        $table->string('notifiable_type'); // App\Models\Admin hoặc App\Models\SinhVien
        $table->string('title');
        $table->text('message')->nullable();
        $table->string('avatar')->nullable();
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
