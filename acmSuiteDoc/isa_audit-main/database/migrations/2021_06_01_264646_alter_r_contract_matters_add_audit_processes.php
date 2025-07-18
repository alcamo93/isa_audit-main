<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRContractMattersAddAuditProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('r_contract_matters', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit_processes')->nullable()->after('id_matter')->comment('Foreign key referring to audit processes table');
        });

        Schema::table('r_contract_matters', function($table) {
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
        Schema::table('r_contract_matters', function (Blueprint $table) {
            $table->dropForeign(['id_audit_processes']);
            $table->dropColumn('id_audit_processes');
        });
    }
}
