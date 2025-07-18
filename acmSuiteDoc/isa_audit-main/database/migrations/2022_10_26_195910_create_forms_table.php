<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('version');
            $table->boolean('is_current')->default(0);
            $table->unsignedBigInteger('matter_id');
            $table->foreign('matter_id')->references('id_matter')->on('c_matters')->onDelete('restrict');
            $table->unsignedBigInteger('aspect_id');
            $table->foreign('aspect_id')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
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
        Schema::dropIfExists('forms');
    }
}
