<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTActionRegistersRemoveIdContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_action_registers', function (Blueprint $table) {
            $table->dropForeign(['id_contract']);
            $table->dropColumn('id_contract');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_action_registers', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contract')->after('id_action_register')->nullable()->comment('Foreign key referring to contracts table'); 
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
        });
    }
}
