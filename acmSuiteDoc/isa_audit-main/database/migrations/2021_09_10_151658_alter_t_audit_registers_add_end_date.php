<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAuditRegistersAddEndDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_audit_registers', function (Blueprint $table) {
            $table->timestampTz('end_date')->after('total')->nullable()->comment('Date end of audit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_audit_registers', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
}
