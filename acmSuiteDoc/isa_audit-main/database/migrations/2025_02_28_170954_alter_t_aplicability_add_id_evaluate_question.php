<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAplicabilityAddIdEvaluateQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->unsignedBigInteger('id_evaluate_question')->after('id_user')->nullable();
            $table->foreign('id_evaluate_question')->references('id')->on('evaluate_applicability_question')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_aplicability', function (Blueprint $table) {
            $table->dropForeign(['id_evaluate_question']);
            $table->dropColumn('id_evaluate_question');
        });
    }
}
