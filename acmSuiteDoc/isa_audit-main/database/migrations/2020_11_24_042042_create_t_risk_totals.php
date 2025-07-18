<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRiskTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_risk_totals', function (Blueprint $table) {
            $table->bigIncrements('id_risk_total');
            $table->integer('total')->default(0)->comment('result of the operation: probability * exhibition * consequence');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requirements table');
            $table->unsignedBigInteger('id_subrequirement')->nullable()->comment('Foreign key referring to requirements table');
            $table->unsignedBigInteger('id_risk_category')->comment('Foreign key referring to risk categories table');
            $table->unsignedBigInteger('id_risk_probability')->comment('Foreign key referring to risk levels in probalities table');
            $table->unsignedBigInteger('id_risk_exhibition')->comment('Foreign key referring to risk levels in exhibitions table');
            $table->unsignedBigInteger('id_risk_consequence')->comment('Foreign key referring to risk levels in consequence table');
            $table->timestamps();
        });
        Schema::table('t_risk_totals', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('restrict');
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('restrict');
            $table->foreign('id_risk_category')->references('id_risk_category')->on('c_risk_categories')->onDelete('restrict');
            $table->foreign('id_risk_probability')->references('id_risk_probability')->on('t_risk_probabilities')->onDelete('restrict');
            $table->foreign('id_risk_exhibition')->references('id_risk_exhibition')->on('t_risk_exhibitions')->onDelete('restrict');
            $table->foreign('id_risk_consequence')->references('id_risk_consequence')->on('t_risk_consequences')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_risk_totals');
    }
}
