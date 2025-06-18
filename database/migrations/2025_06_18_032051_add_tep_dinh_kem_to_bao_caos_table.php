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
       Schema::table('bao_caos', function (Blueprint $table) {
            $table->string('tepDinhKem')->nullable()->after('noiDung'); // hoặc after('ngayTao')
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bao_caos', function (Blueprint $table) {
          $table->dropColumn('tepDinhKem');
        });
    }
};
