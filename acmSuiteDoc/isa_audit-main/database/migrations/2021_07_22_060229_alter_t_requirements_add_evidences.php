<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRequirementsAddEvidences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->dropForeign(['id_audit_type']);
            $table->dropColumn('id_audit_type');
            $table->unsignedBigInteger('id_evidence')->nullable()->after('id_aspect')->comment('Foreign key referring to evidences types table');
            $table->string('document', 400)->nullable()->after('acceptance')->comment('Specific name document about evidence');
        });
        Schema::table('t_requirements', function($table) {
            $table->foreign('id_evidence')->references('id_evidence')->on('c_evidences')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit_type')->nullable()->after('id_aspect')->comment('Foreign key referring to audit types table');
            $table->foreign('id_audit_type')->references('id_audit_type')->on('c_audit_types')->onDelete('restrict');
            $table->dropForeign(['id_evidence']);
            $table->dropColumn('id_evidence');
            $table->dropColumn('document');
        });
    }
}
