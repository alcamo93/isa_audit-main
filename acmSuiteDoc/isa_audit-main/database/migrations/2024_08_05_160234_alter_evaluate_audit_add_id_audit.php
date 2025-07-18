<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEvaluateAuditAddIdAudit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluate_audit_requirement', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit')->after('id_subrequirement')->nullable();
            $table->foreign('id_audit')->references('id_audit')->on('t_audit')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluate_audit_requirement', function (Blueprint $table) {
            $table->dropForeign(['id_audit']);
            $table->dropColumn('id_audit');
        });
    }
}
