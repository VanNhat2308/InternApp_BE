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
       Schema::create('admin_ho_so', function (Blueprint $table) {
        $table->id();
        $table->string('maAdmin');
        $table->string('maHS');

        $table->foreign('maAdmin')->references('maAdmin')->on('admin')->onDelete('cascade');
        $table->foreign('maHS')->references('maHS')->on('ho_sos')->onDelete('cascade');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_ho_so');
    }
};
