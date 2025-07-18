<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluateable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluateables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluateable_id');
            $table->string('evaluateable_type');
            $table->unsignedBigInteger('evaluate_requirement_id');
            $table->index(['evaluateable_id', 'evaluateable_type']);
            $table->foreign('evaluate_requirement_id')->references('id_evaluate_requirement')->on('t_evaluate_requirement')->onDelete('cascade');
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
        Schema::dropIfExists('evaluateables');
    }
}