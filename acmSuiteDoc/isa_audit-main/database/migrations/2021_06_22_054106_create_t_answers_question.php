<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAnswersQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_answers_question', function (Blueprint $table) {
            $table->bigIncrements('id_answer_question');
            $table->text('description')->nullable()->comment('Description text');
            $table->unsignedBigInteger('id_question')->comment('Foreign key referring to questions table');
            $table->unsignedBigInteger('id_status')->nullable()->default(1)->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_answers_question', function($table) {
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('cascade');
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
        Schema::dropIfExists('t_answers_question');
    }
}
