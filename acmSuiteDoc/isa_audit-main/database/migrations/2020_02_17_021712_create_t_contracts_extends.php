<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTContractsExtends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_contracts_extends', function (Blueprint $table) {
            $table->bigIncrements('id_contract_extend')->comment('Primary key referring to contracts extension table');
            $table->string('type')->comment('Type of extension, 0 extension | 1 for renovation');
            $table->timestampTz('start_date')->nullable()->comment('Date of extension beginning');
            $table->timestampTz('end_date')->nullable()->comment('Date for ending extension time');
            $table->string('status')->default(1)->comment('0 used | 1 wating for use');
            $table->unsignedBigInteger('id_contract')->comment('Primary key referring to contracts table');
            $table->unsignedBigInteger('id_license')->comment('Primary key referring to contracts table');
            $table->timestamps();
        });
        Schema::table('t_contracts_extends', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_license')->references('id_license')->on('t_licenses')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_contracts_extends');
    }
}
