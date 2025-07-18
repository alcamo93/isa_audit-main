<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAplicability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_aplicability', function (Blueprint $table) {
            $table->bigIncrements('id_aplicability');
            $table->tinyInteger('answer')->comment('The value 1 refers to the affirmative answer, 0 negative answer');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_question')->comment('Foreign key referring to questions table');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to users table');
            $table->timestamps();
        });
        Schema::table('t_aplicability', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_aplicability');
    }
}
