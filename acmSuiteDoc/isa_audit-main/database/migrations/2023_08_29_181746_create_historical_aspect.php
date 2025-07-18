<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricalAspect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical_aspect', function (Blueprint $table) {
            $table->id();
            $table->double('total');
            $table->dateTime('date');
            $table->unsignedBigInteger('aspect_id');
            $table->foreign('aspect_id')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->unsignedBigInteger('historical_matter_id');
            $table->foreign('historical_matter_id')->references('id')->on('historical_matter')->onDelete('cascade');
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
        Schema::dropIfExists('historical_aspect');
    }
}
