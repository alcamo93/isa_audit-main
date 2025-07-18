<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCApplicationTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_application_types', function (Blueprint $table) {
            $table->bigIncrements('id_application_type')->comment('Primary key referring to aplication types table');
            $table->string('application_type', 50)->comment('Specify the application type textually');
            $table->integer('group')->comment('select which to display');
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
        Schema::dropIfExists('c_application_types');
    }
}
