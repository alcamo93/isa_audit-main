<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->datetime('date');
            $table->boolean('done')->default(false);
            $table->timestamps();

            $table->foreign('task_id')->references('id_task')->on('t_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_notifications');
    }
}
