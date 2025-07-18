<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRQuestionLegalBasies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_question_legal_basies', function (Blueprint $table) {
            $table->bigIncrements('id_question_lb');
            $table->unsignedBigInteger('id_question')->comment('Foreign key referring to questions table');
            $table->unsignedBigInteger('id_legal_basis')->comment('Foreign key referring to legal basises table');
            $table->timestamps();
        });
        Schema::table('r_question_legal_basies', function($table) {
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('cascade');
            $table->foreign('id_legal_basis')->references('id_legal_basis')->on('t_legal_basises')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_question_legal_basies');
    }
}
