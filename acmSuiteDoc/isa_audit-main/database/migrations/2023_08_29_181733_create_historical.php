<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorical extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historical', function (Blueprint $table) {
            $table->id();
            $table->double('total');
            $table->dateTime('date');
            $table->unsignedBigInteger('historicalable_id');
            $table->string('historicalable_type');
            $table->index(['historicalable_id', 'historicalable_type']);
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
        Schema::dropIfExists('historical');
    }
}
