<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTActionPlansAddPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_action_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('id_priority')->default(1)->after('id_status')->comment('Foreign key referring to priorities table');
        });
        Schema::table('t_action_plans', function($table) {
            $table->foreign('id_priority')->references('id_priority')->on('c_priority')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_action_plans', function (Blueprint $table) {
            $table->dropForeign(['id_priority']);
            $table->dropColumn('id_priority');
        });
    }
}
