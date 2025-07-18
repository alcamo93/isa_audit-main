<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_questions', function (Blueprint $table) {
            $table->bigIncrements('id_question')->comment('Primary key referring to questions table');
            $table->text('question')->comment('Specify the question to know what to audit');
            $table->text('help_question')->nullable()->comment('Auditors help');
            $table->integer('order')->comment('Specify the aspect order');
            $table->unsignedBigInteger('id_status')->default(2)->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_matter')->comment('Foreign key referring to matters table');
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_question_type')->comment('Foreign key referring to c_question_types table');
            $table->timestamps();
        });
        Schema::table('t_questions', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_matter')->references('id_matter')->on('c_matters')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_question_type')->references('id_question_type')->on('c_question_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_questions');
    }
}
