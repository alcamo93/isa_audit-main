<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRequirementsAddCity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->unsignedBigInteger('id_city')->nullable()->after('id_state')->comment('Foreign key referring to cities table');
        });

        Schema::table('t_requirements', function($table) {
            $table->foreign('id_city')->references('id_city')->on('c_cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->dropForeign(['id_city']);
            $table->dropColumn('id_city');
        });
    }
}
