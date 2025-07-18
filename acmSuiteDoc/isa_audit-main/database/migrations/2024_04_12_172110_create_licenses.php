<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('num_period');
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('status_id');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('restrict');
            $table->foreign('status_id')->references('id_status')->on('c_status')->onDelete('restrict');
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
        Schema::dropIfExists('licenses');
    }
}
