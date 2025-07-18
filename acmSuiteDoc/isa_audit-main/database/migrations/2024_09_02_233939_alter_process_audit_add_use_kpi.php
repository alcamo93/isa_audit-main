<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProcessAuditAddUseKpi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_audit_processes', function (Blueprint $table) {
            $table->boolean('use_kpi')->nullable()->default(0)->after('evaluation_type_id');
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
            $table->dropColumn('use_kpi');
        });
    }
}
