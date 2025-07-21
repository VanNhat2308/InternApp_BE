<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('login_histories', function (Blueprint $table) {
    $table->id(); 
    $table->string('email');
    $table->enum('loaiNguoiDung', ['admin', 'sinhvien']);
    $table->string('ip_address')->nullable();
    $table->timestamp('login_at')->default(DB::raw('CURRENT_TIMESTAMP'));
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
