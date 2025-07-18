<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTTasksAddBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_tasks', function (Blueprint $table) {
            $table->tinyInteger('block')->default(0)->after('close_date')->comment('The value 1 is block task, 0 no block');
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
            $table->dropColumn('block');
        });
    }
}
