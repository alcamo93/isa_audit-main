<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAplicabilityAddIdAnswerValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->dropColumn('answer');
            $table->unsignedBigInteger('id_answer_value')->after('id_aplicability')->nullable()->comment('Foreign key referring to answer value table');
            $table->unsignedBigInteger('id_answer_question')->after('id_question')->nullable()->comment('Foreign key referring to answer question table');
        });
        Schema::table('t_aplicability', function($table) {
            $table->foreign('id_answer_value')->references('id_answer_value')->on('t_answer_values')->onDelete('restrict');
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
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->tinyInteger('answer')->nullable()->comment('The value 1 refers to the affirmative answer, 0 negative answer');
            $table->dropForeign(['id_answer_value']);
            $table->dropColumn('id_answer_value');
            $table->dropForeign(['id_answer_question']);
            $table->dropColumn('id_answer_question');
        });
    }
}
