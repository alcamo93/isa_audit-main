<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAplicabilityAddIdContractAspect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contract_aspect')->nullable()->after('id_contract')->comment('Foreign key referring to contract aspects table');
        });

        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->foreign('id_contract_aspect')->references('id_contract_aspect')->on('r_contract_aspects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->dropForeign(['id_contract_aspect']);
            $table->dropColumn('id_contract_aspect');
        });
    }
}
