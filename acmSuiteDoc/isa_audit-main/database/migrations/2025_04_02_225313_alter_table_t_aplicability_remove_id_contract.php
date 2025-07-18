<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTAplicabilityRemoveIdContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
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
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->unsignedBigInteger('id_contract')->after('id_answer_value')->nullable();
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('cascade');
        });
    }
}
