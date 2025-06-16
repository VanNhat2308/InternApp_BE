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
        Schema::table('sinh_viens', function (Blueprint $table) {
            $table->string('kyThucTap')->nullable()->after('viTri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinh_viens', function (Blueprint $table) {
                        $table->dropColumn('kyThucTap');
        });
    }
};
