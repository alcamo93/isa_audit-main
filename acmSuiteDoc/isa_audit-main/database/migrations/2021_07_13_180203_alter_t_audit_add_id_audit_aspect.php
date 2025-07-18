<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAuditAddIdAuditAspect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_audit', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit_aspect')->nullable()->after('id_aspect')->comment('Foreign key referring to audit processes table');
        });

        Schema::table('t_audit', function($table) {
            $table->foreign('id_audit_aspect')->references('id_audit_aspect')->on('r_audit_aspects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_audit', function (Blueprint $table) {
            $table->dropForeign(['id_audit_aspect']);
            $table->dropColumn('id_audit_aspect');
        });
    }
}
