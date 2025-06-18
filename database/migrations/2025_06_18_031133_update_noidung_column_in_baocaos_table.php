<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNoidungColumnInBaocaosTable extends Migration
{
    public function up()
    {
        Schema::table('bao_caos', function (Blueprint $table) {
            $table->longText('noiDung')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bao_caos', function (Blueprint $table) {
            $table->text('noiDung')->nullable()->change();
        });
    }
}
