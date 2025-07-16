<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::table('truongs', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('moTa'); // thêm cột logo sau moTa
        });
    }

    public function down(): void
    {
        Schema::table('truongs', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
