<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTActionRegisterAddDatesAndForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_action_registers', function (Blueprint $table) {
            $table->dateTime('init_date')->nullable()->after('id_action_register');
            $table->dateTime('end_date')->nullable()->after('init_date');
            $table->unsignedBigInteger('audit_register_id')->after('id_audit_processes')->nullable();
            $table->foreign('audit_register_id')->references('id_audit_register')->on('t_audit_registers')->onDelete('cascade');
            $table->unsignedBigInteger('obligation_register_id')->after('audit_register_id')->nullable();
            $table->foreign('obligation_register_id')->references('id')->on('obligation_registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_action_registers', function (Blueprint $table) {
            $table->dropColumn('init_date');
            $table->dropColumn('end_date');
            $table->dropForeign(['audit_register_id']);
            $table->dropColumn('audit_register_id');
            $table->dropForeign(['obligation_register_id']);
            $table->dropColumn('obligation_register_id');
        });
    }
}
