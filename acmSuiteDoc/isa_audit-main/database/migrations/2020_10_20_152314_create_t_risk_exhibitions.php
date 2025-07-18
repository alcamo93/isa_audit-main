<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRiskExhibitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_risk_exhibitions', function (Blueprint $table) {
            $table->bigIncrements('id_risk_exhibition');
            $table->integer('exhibition')->comment('Field specifying the level exhibition in number');
            $table->text('name_exhibition')->comment('Field specifying the level exhibition textually');
            $table->unsignedBigInteger('id_risk_category')->comment('Foreign key referring to risk categories table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_risk_exhibitions', function($table) {
            $table->foreign('id_risk_category')->references('id_risk_category')->on('c_risk_categories')->onDelete('cascade');
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
        Schema::dropIfExists('t_risk_exhibitions');
    }
}
