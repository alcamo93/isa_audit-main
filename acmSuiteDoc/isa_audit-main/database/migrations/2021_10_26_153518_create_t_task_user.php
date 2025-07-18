<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTaskUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_task_user', function (Blueprint $table) {
            $table->bigIncrements('id_task_user');
            $table->integer('level')->comment('Specify user level in action plan [1:primary, 2:secondary]');
            $table->integer('days')->nullable()->comment('Specify days to remind before');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_task')->comment('Foreign key referring to action table');
            $table->timestamps();
        });
        Schema::table('t_task_user', function($table) {
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
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
        Schema::dropIfExists('t_task_user');
    }
}
