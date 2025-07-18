<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTActionPlansRemoveUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_action_plans', function (Blueprint $table) {
            $table->dropColumn('export');
            $table->dropForeign(['id_contract']);
            $table->dropColumn('id_contract');
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');
            $table->dropForeign(['id_user_asigned']);
            $table->dropColumn('id_user_asigned');
            $table->dropForeign(['id_recomendation']);
            $table->dropColumn('id_recomendation');
            $table->dropForeign(['id_subrecomendation']);
            $table->dropColumn('id_subrecomendation');
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
            $table->integer('export')->default(0)->after('permit')->comment('Specify it was exported to obligations with value 1 the value 0 it has not been exported');
            $table->unsignedBigInteger('id_contract')->after('export')->nullable()->comment('Foreign key referring to contracts table'); 
            $table->unsignedBigInteger('id_user')->after('id_subrequirement')->nullable()->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_recomendation')->after('id_user')->nullable()->comment('Foreign key referring to requirement recomendations table');
            $table->unsignedBigInteger('id_subrecomendation')->after('id_recomendation')->nullable()->comment('Foreign key referring to subrequirement recomendations table');
            $table->unsignedBigInteger('id_user_asigned')->after('id_subrecomendation')->nullable()->comment('Foreign key referring to users table user who has the asignment');
        });
        Schema::table('t_action_plans', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_recomendation')->references('id_recomendation')->on('t_requirement_recomendations')->onDelete('restrict');
            $table->foreign('id_subrecomendation')->references('id_recomendation')->on('t_subrequirement_recomendations')->onDelete('restrict');
            $table->foreign('id_user_asigned')->references('id_user')->on('t_users')->onDelete('restrict');
        });
    }
}
