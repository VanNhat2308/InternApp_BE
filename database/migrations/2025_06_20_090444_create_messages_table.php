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
       Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->enum('from_role', ['sinhvien', 'admin'])->index();
            $table->unsignedBigInteger('from_id');

            $table->enum('to_role', ['sinhvien', 'admin'])->index();
            $table->unsignedBigInteger('to_id');

            $table->text('content');
            $table->enum('type', ['text', 'image', 'file', 'audio'])->default('text');
            $table->boolean('is_read')->default(false);

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
