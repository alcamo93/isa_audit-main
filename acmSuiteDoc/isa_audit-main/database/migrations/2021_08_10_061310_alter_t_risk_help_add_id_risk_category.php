<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRiskHelpAddIdRiskCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_risk_help', function (Blueprint $table) {
            $table->unsignedBigInteger('id_risk_category')->nullable()->after('id_status')->comment('Foreign key referring to risk categories table');
        });
        Schema::table('t_risk_help', function($table) {
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
        Schema::table('t_risk_help', function (Blueprint $table) {
            $table->dropForeign(['id_risk_category']);
            $table->dropColumn('id_risk_category');
        });
    }
}
