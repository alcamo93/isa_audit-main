<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionPlanRiskTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_plan_risk_total', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_action_plan');
            $table->unsignedBigInteger('id_risk_total');
            $table->foreign('id_action_plan')->references('id_action_plan')->on('t_action_plans')->onDelete('cascade');
            $table->foreign('id_risk_total')->references('id_risk_total')->on('t_risk_totals')->onDelete('cascade');
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
        Schema::dropIfExists('action_plan_risk_total');
    }
}
