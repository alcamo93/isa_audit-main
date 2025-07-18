<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTTasksRemoveUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_tasks', function (Blueprint $table) {
            $table->dropForeign(['id_period']);
            $table->dropColumn('id_period');
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
            $table->dropForeign(['id_user_asigned']);
            $table->dropColumn('id_user_asigned');
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
            $table->unsignedBigInteger('id_period')->nullable()->after('task')->comment('Foreign key referring to periods table');
            $table->unsignedBigInteger('id_user')->nullable()->after('id_period')->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_user_asigned')->nullable()->after('id_user')->comment('Foreign key referring to users table user who has the asignment');
        });
        Schema::table('t_tasks', function($table) {
            $table->foreign('id_period')->references('id_period')->on('c_periods')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_user_asigned')->references('id_user')->on('t_users')->onDelete('restrict');
        });
    }
}
