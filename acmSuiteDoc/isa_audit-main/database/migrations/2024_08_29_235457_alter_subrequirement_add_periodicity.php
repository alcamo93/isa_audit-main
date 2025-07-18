<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubrequirementAddPeriodicity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_subrequirements', function (Blueprint $table) {
            $table->unsignedBigInteger('id_periodicity')->after('id_evidence')->nullable();
            $table->foreign('id_periodicity')->references('id')->on('periodicities')->onDelete('restrict');
            $table->dropForeign(['id_obtaining_period']);
            $table->dropColumn('id_obtaining_period');
            $table->dropForeign(['id_update_period']);
            $table->dropColumn('id_update_period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_subrequirements', function (Blueprint $table) {
            $table->dropForeign(['id_periodicity']);
            $table->dropColumn('id_periodicity');
            $table->unsignedBigInteger('id_obtaining_period');
            $table->foreign('id_obtaining_period')->references('id_period')->on('c_periods')->onDelete('restrict');
            $table->unsignedBigInteger('id_update_period')->nullable();
            $table->foreign('id_update_period')->references('id_period')->on('c_periods')->onDelete('restrict');
        });
    }
}
