<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterChangeDefaultValueObligationStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_obligations', function (Blueprint $table) {
            $table->dropForeign(['id_status']);
            $table->unsignedBigInteger('id_status')->nullable()->default(NULL)->change();
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
        Schema::table('t_obligations', function (Blueprint $table) {
            $table->dropForeign(['id_status']);
            $table->unsignedBigInteger('id_status')->default(20)->change();
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }
}
