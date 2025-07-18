<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAnswerQuestionDependencyConstraitCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_answer_question_dependency', function (Blueprint $table) {
            $table->dropForeign(['id_question']);
            $table->dropForeign(['id_answer_question']);
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('cascade');
            $table->foreign('id_answer_question')->references('id_answer_question')->on('t_answers_question')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_answer_question_dependency', function (Blueprint $table) {
            $table->dropForeign(['id_question']);
            $table->dropForeign(['id_answer_question']);
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('restrict');
            $table->foreign('id_answer_question')->references('id_answer_question')->on('t_answers_question')->onDelete('restrict');
        });
    }
}
