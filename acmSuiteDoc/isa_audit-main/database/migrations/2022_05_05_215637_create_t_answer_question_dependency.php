<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAnswerQuestionDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_answer_question_dependency', function (Blueprint $table) {
            $table->bigIncrements('id_question_dependency');
            $table->unsignedBigInteger('id_question')->comment('Foreign key referring to question table');
            $table->unsignedBigInteger('id_answer_question')->comment('Foreign key referring to answer question table');
        });
        Schema::table('t_answer_question_dependency', function($table) {
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('restrict');
            $table->foreign('id_answer_question')->references('id_answer_question')->on('t_answers_question')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_answer_question_dependency');
    }
}
