<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAspectGuidelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aspect_guideline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aspect_id')->comment('Foreign key referring to c_aspects table');
            $table->unsignedBigInteger('guideline_id')->comment('Foreign key referring to guidelines table');
            $table->timestamps();
           
            $table->foreign('aspect_id')->references('id_aspect')->on('c_aspects')->onDelete('cascade');
            $table->foreign('guideline_id')->references('id_guideline')->on('t_guidelines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aspect_guideline');
    }
}
