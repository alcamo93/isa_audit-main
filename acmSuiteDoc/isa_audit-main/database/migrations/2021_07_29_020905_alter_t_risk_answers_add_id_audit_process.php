<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRiskAnswersAddIdAuditProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_risk_totals', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit')->after('total')->comment('Foreign key referring to audit table');
            $table->unsignedBigInteger('id_audit_processes')->after('id_audit')->comment('Foreign key referring to audit processes table');
            $table->dropForeign(['id_risk_probability']);
            $table->dropColumn('id_risk_probability');
            $table->dropForeign(['id_risk_exhibition']);
            $table->dropColumn('id_risk_exhibition');
            $table->dropForeign(['id_risk_consequence']);
            $table->dropColumn('id_risk_consequence');
            $table->dropForeign(['id_contract']);
            $table->dropColumn('id_contract');
        });
        Schema::table('t_risk_totals', function($table) {
            $table->foreign('id_audit')->references('id_audit')->on('t_audit')->onDelete('cascade');
            $table->foreign('id_audit_processes')->references('id_audit_processes')->on('t_audit_processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_risk_totals', function (Blueprint $table) {
            $table->dropForeign(['id_audit']);
            $table->dropColumn('id_audit');
            $table->dropForeign(['id_audit_processes']);
            $table->dropColumn('id_audit_processes');
            $table->unsignedBigInteger('id_risk_probability')->nullable()->after('id_risk_category')->comment('Foreign key referring to risk levels in probalities table');
            $table->unsignedBigInteger('id_risk_exhibition')->nullable()->after('id_risk_probability')->comment('Foreign key referring to risk levels in exhibitions table');
            $table->unsignedBigInteger('id_risk_consequence')->nullable()->after('id_risk_exhibition')->comment('Foreign key referring to risk levels in consequence table');
            $table->unsignedBigInteger('id_contract')->nullable()->after('total')->comment('Foreign key referring to risk levels in consequence table');
        });
        Schema::table('t_risk_totals', function($table) {
            $table->foreign('id_risk_probability')->references('id_risk_probability')->on('t_risk_probabilities')->onDelete('restrict');
            $table->foreign('id_risk_exhibition')->references('id_risk_exhibition')->on('t_risk_exhibitions')->onDelete('restrict');
            $table->foreign('id_risk_consequence')->references('id_risk_consequence')->on('t_risk_consequences')->onDelete('restrict');
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
        });
    }
}
