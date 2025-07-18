<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTActionExpired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_action_expired', function (Blueprint $table) {
            $table->bigIncrements('id_action_expired')->comment('Primary key referring to action_expired table');
            $table->text('cause')->comment('Cause of deviation');
            $table->dateTime('real_close_date')->comment('Last tentative day for finishing the action');
            $table->tinyInteger('corrective')->default(0)->comment('The value 1 needs corrective, 0 no need');
            $table->unsignedBigInteger('id_action_plan')->unique()->comment('Foreign key referring to action table');
            $table->timestamps();
        });
        Schema::table('t_action_expired', function($table) {
            $table->foreign('id_action_plan')->references('id_action_plan')->on('t_action_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_action_expired');
    }
}
