<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTContractsAddIdLicense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('id_license')->after('end_date')->nullable();
            $table->foreign('id_license')->references('id')->on('licenses')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_contracts', function (Blueprint $table) {
            $table->dropForeign(['id_license']);
            $table->dropColumn('id_license');
        });
    }
}
