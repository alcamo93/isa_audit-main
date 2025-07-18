<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contract');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_user');
    }
}
