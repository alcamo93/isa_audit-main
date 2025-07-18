<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObligationRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obligation_registers', function (Blueprint $table) {
            $table->id();
            $table->double('total')->default(0)->comment('Specify percentage of global compliance');
            $table->dateTime('init_date')->nullable();
            $table->unsignedBigInteger('id_aplicability_register');
            $table->foreign('id_aplicability_register')->references('id_aplicability_register')->on('t_aplicability_registers')->onDelete('cascade');
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
        Schema::dropIfExists('obligation_registers');
    }
}
