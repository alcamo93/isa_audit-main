<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCObligationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_obligation_types', function (Blueprint $table) {
            $table->bigIncrements('id_obligation_type')->comment('Primary key referring to obligation types table');
            $table->string('obligation_type')->comment('Specify the obligation type textually');
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
        Schema::dropIfExists('c_obligation_types');
    }
}
