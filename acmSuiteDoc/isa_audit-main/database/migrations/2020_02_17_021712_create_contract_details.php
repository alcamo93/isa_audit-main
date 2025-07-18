<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_contract_details', function (Blueprint $table) {
            $table->bigIncrements('id_contract_detail');
            $table->string('license', 50)->comment('Name or identifier to licenses. Historical data in contracts');
            $table->integer('usr_global')->commet('Number of users allowed for the global type. Historical data in contracts');
            $table->integer('usr_corporate')->commet('Number of users allowed for the corporate type. Historical data in contracts');
            $table->integer('usr_operative')->comment('Number of users allowed for the operative type. Historical data in contracts');
            $table->unsignedBigInteger('id_period')->comment('Foreign key referring to periods table');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to users table');
            $table->timestamps();
        });
        Schema::table('t_contract_details', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_period')->references('id_period')->on('c_periods')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_details');
    }
}
