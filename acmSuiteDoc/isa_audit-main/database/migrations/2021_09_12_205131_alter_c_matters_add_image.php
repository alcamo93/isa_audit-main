<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCMattersAddImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('c_matters', function (Blueprint $table) {
            $table->string('image', 200)->nullable()->after('description')->default('assets/img/services/default.png')->comment('Image that represents the matter');
            $table->string('color', 50)->nullable()->after('image')->default('#000000')->comment('Color that represents the matter in hexadecimal value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('c_matters', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('color');
        });
    }
}
