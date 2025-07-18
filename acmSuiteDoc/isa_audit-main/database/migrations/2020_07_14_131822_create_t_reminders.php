<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTReminders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_reminders', function (Blueprint $table) {
            $table->bigIncrements('id_reminder');
            $table->dateTime('date')->comment('Reminder date');
            $table->tinyInteger('type_date')->default(0)->comment('The value 1 close date, 0 is real close date');
            $table->unsignedBigInteger('id_action_plan')->nullable()->comment('Foreign key referring to action plans table');
            $table->unsignedBigInteger('id_obligation')->nullable()->comment('Foreign key referring to comment table');
            $table->unsignedBigInteger('id_task')->nullable()->comment('Foreign key referring to tasks table');
            $table->timestamps();
        });
        Schema::table('t_reminders', function($table) {
            $table->foreign('id_action_plan')->references('id_action_plan')->on('t_action_plans')->onDelete('cascade');
            $table->foreign('id_obligation')->references('id_obligation')->on('t_obligations')->onDelete('cascade');
            $table->foreign('id_task')->references('id_task')->on('t_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_reminders');
    }
}
