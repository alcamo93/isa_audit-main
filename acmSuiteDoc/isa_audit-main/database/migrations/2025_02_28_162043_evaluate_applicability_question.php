<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EvaluateApplicabilityQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluate_applicability_question', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('complete')->default(0)->comment('The value 1 is complete, 0 no complete');
            $table->unsignedBigInteger('id_contract_aspect')->nullable()->comment('Foreign key referring to contract aspect');
            $table->unsignedBigInteger('id_question')->comment('Foreign key referring to question to evaluate');
            $table->foreign('id_contract_aspect')->references('id_contract_aspect')->on('r_contract_aspects')->onDelete('cascade');
            $table->foreign('id_question')->references('id_question')->on('t_questions')->onDelete('restrict');
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
        Schema::dropIfExists('evaluate_applicability_question');
    }
}
