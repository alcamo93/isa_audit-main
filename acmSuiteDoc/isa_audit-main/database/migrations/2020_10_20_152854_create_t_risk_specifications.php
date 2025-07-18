<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRiskSpecifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_risk_specifications', function (Blueprint $table) {
            $table->bigIncrements('id_risk_specification');
            $table->text('specification')->comment('Describe specifications for each risk level');
            $table->unsignedBigInteger('id_risk_probability')->nullable()->comment('Foreign key referring to risk levels in probalities table');
            $table->unsignedBigInteger('id_risk_exhibition')->nullable()->comment('Foreign key referring to risk levels in exhibitions table');
            $table->unsignedBigInteger('id_risk_consequence')->nullable()->comment('Foreign key referring to risk levels in consequence table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_risk_specifications', function($table) {
            $table->foreign('id_risk_probability')->references('id_risk_probability')->on('t_risk_probabilities')->onDelete('cascade');
            $table->foreign('id_risk_exhibition')->references('id_risk_exhibition')->on('t_risk_exhibitions')->onDelete('cascade');
            $table->foreign('id_risk_consequence')->references('id_risk_consequence')->on('t_risk_consequences')->onDelete('cascade');
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
        Schema::dropIfExists('t_risk_specifications');
    }
}
