<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTCommentsAddStage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_comments', function (Blueprint $table) {
            $table->tinyInteger('stage')->nullable()->default(1)->after('comment')->comment('If the value is 1 the task was created in normal stage, if the value is 2 the task was created in overdue stage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_comments', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
}
