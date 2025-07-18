<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTFilesDen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_files_den', function (Blueprint $table) {
            $table->bigIncrements('id_file');
            $table->string('title')->comment('File title');
            $table->string('url')->nullable()->comment('Url it depends from the origin of the file (comments_obligation/comments_ap/obligation/action_plan/tasks)');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to user table');
            $table->unsignedBigInteger('id_comment')->nullable()->comment('Foreign key referring to comment table');
            $table->unsignedBigInteger('id_action_plan')->nullable()->comment('Foreign key referring to action plans table');
            $table->unsignedBigInteger('id_obligation')->nullable()->comment('Foreign key referring to comment table');
            $table->unsignedBigInteger('id_task')->nullable()->comment('Foreign key referring to tasks table');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        Schema::table('t_files_den', function($table) {
            $table->foreign('id_comment')->references('id_comment')->on('t_comments')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_action_plan')->references('id_action_plan')->on('t_action_plans')->onDelete('cascade');
            $table->foreign('id_obligation')->references('id_obligation')->on('t_obligations')->onDelete('cascade');
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
        Schema::dropIfExists('t_files_den');
    }
}
