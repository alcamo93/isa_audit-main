<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_addresses', function (Blueprint $table) {
            $table->bigIncrements('id_address')->comment('Primary key referring to addresses table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->string('street', 150)->nullable()->commment('Specifies the name of the street');
            $table->string('ext_num', 60)->nullable()->commment('Specifies the external number of the street');
            $table->string('int_num', 60)->nullable()->commment('Specifies the internal number of the street');
            $table->string('zip', 10)->nullable()->commment('Specifies the zip of the address');
            $table->string('suburb', 50)->nullable()->commment('Specifies the name of the suburb');
            $table->tinyInteger('type')->default(0)->comment('The value 1 refers to the physical address, 0 to the fiscal address');
            $table->unsignedBigInteger('id_country')->comment('Foreign key referring to country table');
            $table->unsignedBigInteger('id_state')->comment('Foreign key referring to states table');
            $table->unsignedBigInteger('id_city')->comment('Foreign key referring to cities table');
            $table->timestamps();
        });
        Schema::table('t_addresses', function($table) {
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('cascade');
            $table->foreign('id_country')->references('id_country')->on('c_countries')->onDelete('restrict');
            $table->foreign('id_state')->references('id_state')->on('c_states')->onDelete('restrict');
            $table->foreign('id_city')->references('id_city')->on('c_cities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_addresses');
    }
}
