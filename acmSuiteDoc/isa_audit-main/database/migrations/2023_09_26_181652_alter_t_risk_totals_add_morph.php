<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTRiskTotalsAddMorph extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_risk_totals', function (Blueprint $table) {
            $table->unsignedBigInteger('registerable_id')->after('id_audit')->nullable();
            $table->string('registerable_type')->after('registerable_id')->nullable();
            $table->index(['registerable_id', 'registerable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_risk_totals', function (Blueprint $table) {
            $table->dropColumn('registerable_id');
            $table->dropColumn('registerable_type');
        });
    }
}
