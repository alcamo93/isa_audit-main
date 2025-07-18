<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCQuestionTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_question_types', function (Blueprint $table) {
            $table->bigIncrements('id_question_type')->comment('Primary key referring to question_types table');
            $table->string('question_type')->comment('Specify the questions_type textually');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_question_types');
    }
}
