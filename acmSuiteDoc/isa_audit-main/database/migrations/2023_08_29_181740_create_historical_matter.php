<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalMatter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_matter', function (Blueprint $table) {
            $table->id();
            $table->double('total');
            $table->dateTime('date');
            $table->unsignedBigInteger('matter_id');
            $table->foreign('matter_id')->references('id_matter')->on('c_matters')->onDelete('restrict');
            $table->unsignedBigInteger('historical_id');
            $table->foreign('historical_id')->references('id')->on('historical')->onDelete('cascade');
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
        Schema::dropIfExists('historical_matter');
    }
}
