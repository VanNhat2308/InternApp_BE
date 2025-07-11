<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
    {
        Schema::table('schedule_swaps', function (Blueprint $table) {
            $table->string('maLich')->nullable()->after('maSV');

            // Nếu bạn muốn thiết lập ràng buộc khóa ngoại
            $table->foreign('maLich')->references('maLich')->on('lichs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('schedule_swaps', function (Blueprint $table) {
            $table->dropForeign(['maLich']);
            $table->dropColumn('maLich');
        });
    }
};
