<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTQuestionsAddLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('id_state')->nullable()->after('id_question_type')->comment('Foreign key referring to state table');
            $table->unsignedBigInteger('id_city')->nullable()->after('id_state')->comment('Foreign key referring to cities table');
        });

        Schema::table('t_questions', function($table) {
            $table->foreign('id_state')->references('id_state')->on('c_states')->onDelete('restrict');
            $table->foreign('id_city')->references('id_city')->on('c_cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_questions', function (Blueprint $table) {
            $table->dropForeign(['id_state']);
            $table->dropColumn('id_state');
            $table->dropForeign(['id_city']);
            $table->dropColumn('id_city');
        });
    }
}
