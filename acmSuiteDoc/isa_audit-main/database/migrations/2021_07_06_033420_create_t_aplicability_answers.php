<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAplicabilityAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_aplicability_answers', function (Blueprint $table) {
            $table->bigIncrements('id_aplicability_answer');
            $table->unsignedBigInteger('id_answer_question')->comment('Foreign key referring to multi answers questions table');
            $table->unsignedBigInteger('id_aplicability')->comment('Foreign key referring to aplicability table');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_audit_processes')->comment('Foreign key referring to audit processes table');        
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to users table');
            $table->timestamps();
        });
        Schema::table('t_aplicability_answers', function($table) {
            $table->foreign('id_answer_question')->references('id_answer_question')->on('t_answers_question')->onDelete('restrict');
            $table->foreign('id_aplicability')->references('id_aplicability')->on('t_aplicability')->onDelete('cascade');
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_audit_processes')->references('id_audit_processes')->on('t_audit_processes')->onDelete('cascade');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
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
        Schema::dropIfExists('t_aplicability_answers');
    }
}
