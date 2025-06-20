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
          Schema::table('lichs', function (Blueprint $table) {
        $table->string('time')->nullable();     // Ví dụ: "08:00"
        $table->integer('duration')->default(4); // Số giờ
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lichs', function (Blueprint $table) {
            //
        });
    }
};
