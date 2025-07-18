<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTTasksAddDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_tasks', function (Blueprint $table) {
            $table->dateTime('init_date')->after('task')->nullable()->comment('The beginning day, tasks starts this day');
            $table->dateTime('close_date')->after('init_date')->nullable()->comment('Last tentative day for finishing the tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_tasks', function (Blueprint $table) {
            $table->dropColumn('init_date');
            $table->dropColumn('close_date');
        });
    }
}
