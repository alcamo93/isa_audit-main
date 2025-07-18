<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterObligationsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('t_obligations', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('obligation');
            $table->dropColumn('permit');
            $table->dropColumn('renewal_date');
            $table->dropColumn('last_renewal_date');
            $table->dropForeign(['id_period']);
            $table->dropColumn('id_period');
            $table->dropForeign(['id_condition']);
            $table->dropColumn('id_condition');
            $table->dropForeign(['id_obligation_type']);
            $table->dropColumn('id_obligation_type');
            $table->dropForeign(['id_action_register']);
            $table->dropColumn('id_action_register');
            $table->dropForeign(['id_audit_processes']);
            $table->dropColumn('id_audit_processes');
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
            $table->dropForeign(['id_user_asigned']);
            $table->dropColumn('id_user_asigned');
            // new tabla
            $table->dateTime('end_date')->nullable()->after('init_date');
            $table->unsignedBigInteger('obligation_register_id')->after('end_date')->nullable();
            $table->foreign('obligation_register_id')->references('id')->on('obligation_registers')->onDelete('cascade');
            $table->unsignedBigInteger('id_requirement')->after('obligation_register_id')->nullable();
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('restrict');
            $table->unsignedBigInteger('id_subrequirement')->after('id_requirement')->nullable();
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('restrict');
        });
        // \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_obligations', function (Blueprint $table) {
            $table->bigIncrements('id_obligation')->comment('Primary key referring to obligation table');
            $table->text('title')->comment('Obligation title');
            $table->text('obligation')->comment('Obligation text');
            $table->dateTime('init_date')->nullable()->comment('The beginning day, initial day for the obligation');
            $table->dateTime('renewal_date')->nullable()->comment('Renewal day for the obligation');
            $table->dateTime('last_renewal_date')->nullable()->comment('Last day limit for the obligation');
            $table->integer('permit')->default(0)->comment('if the value is 0 it can not modify dates, if it is 1 it can be able to modify the date, if it is 2 the request is pending');
            $table->unsignedBigInteger('id_status')->default(20)->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_period')->comment('Foreign key referring to periods table');
            $table->unsignedBigInteger('id_condition')->comment('Foreign key referring to conditions table');
            $table->unsignedBigInteger('id_action_register')->comment('Foreign key referring to action plans table');
            $table->unsignedBigInteger('id_user')->nullable()->comment('Foreign key referring to users table user who mades the asignment');
            $table->unsignedBigInteger('id_user_asigned')->nullable()->comment('Foreign key referring to users table user who has the asignment');
            $table->timestamps();
        });
        Schema::table('t_obligations', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_period')->references('id_period')->on('c_periods')->onDelete('restrict');
            $table->foreign('id_condition')->references('id_condition')->on('c_conditions')->onDelete('restrict');
            $table->foreign('id_action_register')->references('id_action_register')->on('t_action_registers')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_user_asigned')->references('id_user')->on('t_users')->onDelete('restrict');
        });
    }
}
