<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_comments', function (Blueprint $table) {
            $table->bigIncrements('id_comment');
            $table->string('title')->comment('Shows a comment about the obligation/action plan');
            $table->string('comment')->comment('Shows a comment about the obligation/action plan');
            $table->tinyInteger('origin')->comment('The value 0 refers to the action_plan, 1 to the obligation');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to user');
            $table->unsignedBigInteger('id_action_plan')->nullable()->comment('Foreign key referring to obligation');
            $table->unsignedBigInteger('id_obligation')->nullable()->comment('Foreign key referring to obligation');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        Schema::table('t_comments', function($table) {
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_action_plan')->references('id_action_plan')->on('t_action_plans')->onDelete('cascade');
            $table->foreign('id_obligation')->references('id_obligation')->on('t_obligations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_comments');
    }
}
