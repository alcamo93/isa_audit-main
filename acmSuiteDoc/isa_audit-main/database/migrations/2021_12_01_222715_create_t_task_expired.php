<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTaskExpired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_task_expired', function (Blueprint $table) {
            $table->bigIncrements('id_task_expired');
            $table->dateTime('init_date')->comment('Last init day for finishing the task');
            $table->dateTime('close_date')->nullable()->comment('Last tentative day for finishing the task');
            $table->unsignedBigInteger('id_status')->default(13)->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_task')->unique()->comment('Foreign key referring to task table');
            $table->timestamps();
        });
        Schema::table('t_task_expired', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
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
        Schema::dropIfExists('t_task_expired');
    }
}
