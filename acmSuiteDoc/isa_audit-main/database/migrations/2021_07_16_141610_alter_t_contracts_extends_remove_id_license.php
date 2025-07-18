<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTContractsExtendsRemoveIdLicense extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_contracts_extends', function (Blueprint $table) {
            $table->dropForeign(['id_license']);
            $table->dropColumn('id_license');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_contracts_extends', function (Blueprint $table) {
            $table->unsignedBigInteger('id_license')->nullable()->after('id_contract')->comment('Foreign key referring to license table');
        });
        Schema::table('t_contracts_extends', function($table) {
            $table->foreign('id_license')->references('id_license')->on('t_licenses')->onDelete('restrict');
        });
    }
}
