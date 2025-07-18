<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTTaskAddMainTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_tasks', function (Blueprint $table) {
            $table->tinyInteger('main_task')->default(0)->after('stage')->comment('The value 1 is main task, 0 no main');
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
            $table->dropColumn('main_task');
        });
    }
}
