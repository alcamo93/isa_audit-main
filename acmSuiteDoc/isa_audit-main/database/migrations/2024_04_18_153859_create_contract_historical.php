<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractHistorical extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_historical', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sequence');
            $table->unsignedBigInteger('type');
            $table->timestampTz('start_date')->nullable();
            $table->timestampTz('end_date')->nullable();
            $table->integer('num_period');
            $table->unsignedBigInteger('id_period');
            $table->unsignedBigInteger('id_status');
            $table->unsignedBigInteger('id_contract');
            $table->foreign('id_period')->references('id')->on('periods')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_historical');
    }
}
