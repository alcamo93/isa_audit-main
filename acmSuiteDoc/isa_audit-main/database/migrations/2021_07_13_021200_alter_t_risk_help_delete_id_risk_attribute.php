<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRiskHelpDeleteIdRiskAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_risk_help', function (Blueprint $table) {
            $table->dropColumn('id_risk_attribute');
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
            $table->bigInteger('id_risk_attribute')->comment('Attribute ID');
        });
    }
}
