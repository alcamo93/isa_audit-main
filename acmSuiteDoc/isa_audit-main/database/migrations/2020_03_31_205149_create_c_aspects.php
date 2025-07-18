<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCAspects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_aspects', function (Blueprint $table) {
            $table->bigIncrements('id_aspect')->comment('Primary key referring to aspects table');
            $table->string('aspect', 50)->comment('Specify the aspect');
            $table->integer('order')->comment('Specify the aspect order');
            $table->unsignedBigInteger('id_matter')->comment('Foreign key referring to matters table');
            $table->timestamps();
        });
        Schema::table('c_aspects', function($table) {
            $table->foreign('id_matter')->references('id_matter')->on('c_matters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_aspects');
    }
}
