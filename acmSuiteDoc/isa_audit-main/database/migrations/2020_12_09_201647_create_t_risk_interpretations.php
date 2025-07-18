<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRiskInterpretations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_risk_interpretations', function (Blueprint $table) {
            $table->bigIncrements('id_risk_interpretation');
            $table->text('interpretation')->comment('Describe interpretation');
            $table->integer('interpretation_min')->comment('Describe min limit for each numeric value');
            $table->integer('interpretation_max')->comment('Describe max limit for each numeric value');
            $table->unsignedBigInteger('id_risk_category')->comment('Foreign key referring to risk categories table');
            $table->timestamps();
        });
        Schema::table('t_risk_interpretations', function($table) {
            $table->foreign('id_risk_category')->references('id_risk_category')->on('c_risk_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_risk_interpretations');
    }
}
