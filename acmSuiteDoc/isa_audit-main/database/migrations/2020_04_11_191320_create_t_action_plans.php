<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTActionPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_action_plans', function (Blueprint $table) {
            $table->bigIncrements('id_action_plan')->comment('Primary key referring to action planification table');
            $table->dateTime('init_date')->nullable()->comment('The beginning day, action plan starts this day');
            $table->dateTime('close_date')->nullable()->comment('Last tentative day for finishing the action plan');
            $table->dateTime('real_close_date')->nullable()->comment('The action plan must be finished at this date');
            $table->text('finding')->nullable()->comment('Specify the finding in requirement requested in the audit');
            $table->unsignedInteger('total_tasks')->default(0)->comment('Specify the total tasks in tasks table');
            $table->unsignedInteger('done_tasks')->default(0)->comment('Specify the total tasks in tasks table with id_state 12');
            $table->integer('complex')->default(0)->comment('Specify if complex is 0 the requirement is only a task, 1 multiple tasks');
            $table->integer('permit')->default(0)->comment('if the value is 0 it can not modify dates, if it is 1 it can be able to modify the date, if it is 2 the request is pending');
            $table->integer('export')->default(0)->comment('Specify it was exported to obligations with value 1 the value 0 it has not been exported');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table'); 
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requirements table');
            $table->unsignedBigInteger('id_subrequirement')->nullable()->comment('Foreign key referring to requirements table');
            $table->unsignedBigInteger('id_user')->nullable()->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_recomendation')->nullable()->comment('Foreign key referring to requirement recomendations table');
            $table->unsignedBigInteger('id_subrecomendation')->nullable()->comment('Foreign key referring to subrequirement recomendations table');
            $table->unsignedBigInteger('id_action_register')->comment('Foreign key referring to action plans table');
            $table->unsignedBigInteger('id_user_asigned')->nullable()->comment('Foreign key referring to users table user who has the asignment');
            $table->unsignedBigInteger('id_status')->nullable()->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_action_plans', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('restrict');
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_recomendation')->references('id_recomendation')->on('t_requirement_recomendations')->onDelete('restrict');
            $table->foreign('id_subrecomendation')->references('id_recomendation')->on('t_subrequirement_recomendations')->onDelete('restrict');
            $table->foreign('id_action_register')->references('id_action_register')->on('t_action_registers')->onDelete('cascade');
            $table->foreign('id_user_asigned')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_action_plans');
    }
}