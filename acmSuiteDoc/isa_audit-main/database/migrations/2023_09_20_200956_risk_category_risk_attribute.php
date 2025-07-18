<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RiskCategoryRiskAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_category_risk_attribute', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_risk_category');
            $table->unsignedBigInteger('id_risk_attribute');
            $table->foreign('id_risk_category')->references('id_risk_category')->on('c_risk_categories')->onDelete('cascade');
            $table->foreign('id_risk_attribute')->references('id_risk_attribute')->on('c_risk_attributes')->onDelete('restrict');
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
        Schema::dropIfExists('risk_category_risk_attribute');
    }
}
