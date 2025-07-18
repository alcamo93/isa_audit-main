<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTObligations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_obligations', function (Blueprint $table) {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_obligations');
    }
}