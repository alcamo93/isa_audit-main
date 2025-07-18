<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_cities', function (Blueprint $table) {
            $table->bigIncrements('id_city')->comment('Primary key referring to cities table');
            $table->unsignedBigInteger('id_state')->comment('Foreign key referring to states table');
            $table->string('city',100)->comment('City name');
            $table->timestamps();
        });
        Schema::table('c_cities', function($table) {
            $table->foreign('id_state')->references('id_state')->on('c_states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_cities');
    }
}
