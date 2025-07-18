<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTContractsExtendsIdContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_contracts_extends', function (Blueprint $table) {
            $table->dropForeign(['id_contract']);
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_contracts_extends', function (Blueprint $table) {
            $table->dropForeign(['id_contract']);
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
        });
    }
}
