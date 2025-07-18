<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAplicabilityAddIdAuditProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit_processes')->nullable()->after('id_contract')->comment('Foreign key referring to audit processes table');
        });

        Schema::table('t_aplicability', function($table) {
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
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->dropForeign(['id_audit_processes']);
            $table->dropColumn('id_audit_processes');
        });
    }
}
