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
        Schema::create('truongs', function (Blueprint $table) {
        $table->id();
        $table->string('maTruong')->unique(); // Mã trường (unique)
        $table->string('tenTruong');
        $table->text('moTa')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truongs');
    }
};
