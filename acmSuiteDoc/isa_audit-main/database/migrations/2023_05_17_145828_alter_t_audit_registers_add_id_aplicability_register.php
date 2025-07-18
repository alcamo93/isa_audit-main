<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAuditRegistersAddIdAplicabilityRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_audit_registers', function (Blueprint $table) {
            $table->dateTime('init_date')->nullable()->after('total');
            $table->unsignedBigInteger('id_aplicability_register')->after('end_date')->nullable();
            $table->foreign('id_aplicability_register')->references('id_aplicability_register')->on('t_aplicability_registers')->onDelete('cascade');
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
            $table->dropForeign(['id_aplicability_register']);
            $table->dropColumn('id_aplicability_register');
            $table->dropColumn('init_date');
        });
    }
}
