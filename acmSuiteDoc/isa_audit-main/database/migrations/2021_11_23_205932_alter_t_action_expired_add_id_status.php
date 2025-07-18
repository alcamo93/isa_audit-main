<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTActionExpiredAddIdStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_action_expired', function (Blueprint $table) {
            $table->unsignedBigInteger('id_status')->default(13)->after('id_action_plan')->comment('Foreign key referring to action table');
        });
        Schema::table('t_action_expired', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_action_expired', function (Blueprint $table) {
            $table->dropForeign(['id_status']);
            $table->dropColumn('id_status');
        });
    }
}
