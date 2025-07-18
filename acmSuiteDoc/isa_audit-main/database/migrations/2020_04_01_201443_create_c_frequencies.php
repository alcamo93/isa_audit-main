<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCFrequencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_frequencies', function (Blueprint $table) {
            $table->bigIncrements('id_frequency')->comment('Primary key referring to matters table');
            $table->string('frequency')->comment('Specify the application type textually');
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
        Schema::dropIfExists('c_frequencies');
    }
}
