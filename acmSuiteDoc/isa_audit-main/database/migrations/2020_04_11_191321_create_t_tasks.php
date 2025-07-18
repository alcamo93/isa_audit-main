<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_tasks', function (Blueprint $table) {
            $table->bigIncrements('id_task')->comment('Primary key referring to action planification table');
            $table->string('title')->comment('Title to identify task');
            $table->text('task')->comment('Specify the finding in requirement requested in the audit');
            $table->unsignedBigInteger('id_period')->comment('Foreign key referring to periods table');
            $table->unsignedBigInteger('id_action_plan')->comment('Foreign key referring to action plans table');
            $table->unsignedBigInteger('id_user')->nullable()->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_user_asigned')->nullable()->comment('Foreign key referring to users table user who has the asignment');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_tasks', function($table) {
            $table->foreign('id_period')->references('id_period')->on('c_periods')->onDelete('restrict');
            $table->foreign('id_action_plan')->references('id_action_plan')->on('t_action_plans')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
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
        Schema::dropIfExists('t_tasks');
    }
}