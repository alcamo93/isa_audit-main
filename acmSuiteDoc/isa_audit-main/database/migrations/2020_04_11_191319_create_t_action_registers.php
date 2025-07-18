<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTActionRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_action_registers', function (Blueprint $table) {
            $table->bigIncrements('id_action_register')->comment('Primary key referring to action_registers table');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_action_registers', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_action_registers');
    }
}
