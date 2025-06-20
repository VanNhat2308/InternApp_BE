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
       Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->enum('user1_role', ['sinhvien', 'admin'])->index();
            $table->unsignedBigInteger('user1_id');

            $table->enum('user2_role', ['sinhvien', 'admin'])->index();
            $table->unsignedBigInteger('user2_id');

            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('last_message_id')->references('id')->on('messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
