<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddAplicabilityRegisterIdEvaluateRequirement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_evaluate_requirement', function (Blueprint $table) {
            $table->tinyInteger('show_library')->after('complete')->nullable()->default(1);
            $table->unsignedBigInteger('aplicability_register_id')->after('id_contract_aspect')->nullable();
            $table->foreign('aplicability_register_id')->references('id_aplicability_register')->on('t_aplicability_registers')->onDelete('cascade');
            $table->dropForeign(['id_audit_aspect']);
            $table->dropColumn('id_audit_aspect');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_evaluate_requirement', function (Blueprint $table) {
            $table->dropColumn('show_library');
            $table->dropForeign(['aplicability_register_id']);
            $table->dropColumn('aplicability_register_id');
            $table->unsignedBigInteger('id_audit_aspect')->after('id_contract_aspect')->nullable()->comment('Foreign key referring to audit aspect');
            $table->foreign('id_audit_aspect')->references('id_audit_aspect')->on('r_audit_aspects')->onDelete('cascade');
        });
    }
}
