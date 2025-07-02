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
        Schema::create('chi_tiet_nhat_kies', function (Blueprint $table) {
            $table->id();
            $table->string('tenCongViec');
            $table->text('ketQua')->nullable();
            $table->enum('tienDo', ['Hoàn thành', 'Chưa xong']);

            $table->string('maNK'); // liên kết với nhật ký
            $table->foreign('maNK')->references('maNK')->on('nhat_kies')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_nhat_kies');
    }
};
