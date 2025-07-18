<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_countries', function (Blueprint $table) {
            $table->bigIncrements('id_country')->comment('Primary key referring to countries table');
            $table->string('country', 45)->comment('Country name');
            $table->string('country_code', 5)->comment('Country code');
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
        Schema::dropIfExists('c_countries');
    }
}
