<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRQuestionRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_question_requirements', function (Blueprint $table) {
            $table->bigIncrements('id_question_requirement');
            $table->unsignedBigInteger('id_question')->comment('Foreign key referring to questions table');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requerimients table');
            $table->unsignedBigInteger('id_subrequirement')->nullable()->comment('Foreign key referring to subrequerimients table');
            $table->timestamps();
        });
        Schema::table('r_question_requirements', function($table) {
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('cascade');
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('cascade');
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_question_requirements');
    }
}
