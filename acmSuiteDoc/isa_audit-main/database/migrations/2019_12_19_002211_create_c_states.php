<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_states', function (Blueprint $table) {
            $table->bigIncrements('id_state')->comment('Primary key referring to states table');
            $table->unsignedBigInteger('id_country')->comment('Foreign key referring to countries table');
            $table->string('state', 45)->comment('State name');
            $table->string('state_code', 6)->comment('State code');
            $table->timestamps();
        });
        Schema::table('c_states', function($table) {
            $table->foreign('id_country')->references('id_country')->on('c_countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_states');
    }
}
