<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('diemSo')->nullable()->after('noiDung');
            $table->enum('doUuTien', ['Cao', 'Trung bình', 'Thấp'])->nullable(); // Có thể dùng enum nếu bạn muốn giới hạn giá trị
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['diemSo', 'doUuTien']);
        });
    }
};
