<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAnswersQuestionAddIdAnswerValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_answers_question', function (Blueprint $table) {
            $table->unsignedBigInteger('id_answer_value')->nullable()->default(1)->after('id_question')->comment('Foreign key referring to answer value table');
        });
        Schema::table('t_answers_question', function($table) {
            $table->foreign('id_answer_value')->references('id_answer_value')->on('t_answer_values')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_answers_question', function (Blueprint $table) {
            $table->dropForeign(['id_answer_value']);
            $table->dropColumn('id_answer_value');
        });
    }
}
