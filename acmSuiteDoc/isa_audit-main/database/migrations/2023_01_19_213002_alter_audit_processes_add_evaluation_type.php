<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAuditProcessesAddEvaluationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_audit_processes', function (Blueprint $table) {
            $table->integer('per_year')->nullable()->default(1)->after('evaluate_risk');
            $table->dateTime('date')->nullable()->after('per_year');
            $table->boolean('evaluate_especific')->nullable()->default(false)->after('date');
            $table->unsignedBigInteger('evaluation_type_id')->nullable()->after('evaluate_especific')->comment('Reference to evaluation_types table');
            $table->foreign('evaluation_type_id')->references('id')->on('evaluation_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_audit_processes', function (Blueprint $table) {
            $table->dropForeign(['evaluation_type_id']);
            $table->dropColumn('evaluation_type_id');
            $table->dropColumn('evaluate_especific');
            $table->dropColumn('per_year');
            $table->dropColumn('date');
        });
    }
}
