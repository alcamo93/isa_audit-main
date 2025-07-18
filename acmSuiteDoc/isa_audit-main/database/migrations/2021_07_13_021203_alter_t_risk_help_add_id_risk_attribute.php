<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRiskHelpAddIdRiskAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_risk_help', function (Blueprint $table) {
            $table->unsignedBigInteger('id_risk_attribute')->after('id_status')->comment('Foreign key referring to risk attribute table');
        });
        Schema::table('t_risk_help', function (Blueprint $table) {
            $table->foreign('id_risk_attribute')->references('id_risk_attribute')->on('c_risk_attributes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_risk_help', function (Blueprint $table) {
            $table->dropForeign(['id_risk_attribute']);
            $table->dropColumn('id_risk_attribute');
        });
    }
}
