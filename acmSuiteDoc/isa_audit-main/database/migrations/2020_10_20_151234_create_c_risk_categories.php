<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCRiskCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_risk_categories', function (Blueprint $table) {
            $table->bigIncrements('id_risk_category');
            $table->string('risk_category')->comment('Name risk category');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('c_risk_categories', function($table) {
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
        Schema::dropIfExists('c_risk_categories');
    }
}
