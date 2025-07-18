<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTCommentsAddIdTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('id_task')->nullable()->after('id_action_plan')->comment('Foreign key referring to tasks table');
            $table->dropColumn('title');
            $table->dropColumn('origin');
        });
        Schema::table('t_comments', function($table) {
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
        Schema::table('t_comments', function (Blueprint $table) {
            $table->dropForeign(['id_task']);
            $table->dropColumn('id_task');
            $table->string('title')->nullable()->after('id_comment')->comment('Shows a comment about the obligation/action plan');
            $table->tinyInteger('origin')->nullable()->after('comment')->comment('The value 0 refers to the action_plan, 1 to the obligation');
        });
    }
}
