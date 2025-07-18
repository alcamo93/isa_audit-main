<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTObligationsAddIdObligationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_obligations', function (Blueprint $table) {
            $table->unsignedBigInteger('id_obligation_type')->nullable()->after('id_condition')->comment('Foreign key referring to obligations table');
        });
        Schema::table('t_obligations', function($table) {
            $table->foreign('id_obligation_type')->references('id_obligation_type')->on('c_obligation_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_obligations', function (Blueprint $table) {
            $table->dropForeign(['id_obligation_type']);
            $table->dropColumn('id_obligation_type');
        });
    }
}
