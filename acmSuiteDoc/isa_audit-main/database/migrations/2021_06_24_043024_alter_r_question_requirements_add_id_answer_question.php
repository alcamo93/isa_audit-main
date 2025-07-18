<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRQuestionRequirementsAddIdAnswerQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('r_question_requirements', function (Blueprint $table) {
            $table->unsignedBigInteger('id_answer_question')->nullable()->after('id_question')->comment('Foreign key referring to answer questions table');
        });
        Schema::table('r_question_requirements', function($table) {
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
        Schema::table('r_question_requirements', function (Blueprint $table) {
            $table->dropForeign(['id_answer_question']);
            $table->dropColumn('id_answer_question');
        });
    }
}
