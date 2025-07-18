<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRiskHelp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_risk_help', function (Blueprint $table) {
            $table->bigIncrements('id_risk_help');
            $table->text('risk_help')->comment('Name risk category');
            $table->text('standard')->comment('Description of the value');
            $table->double('value')->comment('Value to be selected in any risk');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->bigInteger('id_risk_attribute')->comment('Attribute ID');
            $table->timestamps();
        });
        Schema::table('t_risk_help', function($table) {
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
        Schema::dropIfExists('t_risk_help');
    }
}
