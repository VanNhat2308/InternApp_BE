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
    Schema::table('messages', function (Blueprint $table) {
        $table->unsignedBigInteger('conversation_id')->after('to_id');

        // Nếu muốn ràng buộc khóa ngoại:
        // $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn('conversation_id');
    });
}

 
};
