<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidelineExtGuidelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guideline_ext_guideline', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guideline_id')->comment('Foreign key referring to guidelines table');
            $table->unsignedBigInteger('ext_guideline_id')->comment('Foreign key referring to guidelines table');
            $table->timestamps();

            $table->foreign('guideline_id')->references('id_guideline')->on('t_guidelines')->onDelete('cascade');
            $table->foreign('ext_guideline_id')->references('id_guideline')->on('t_guidelines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guideline_ext_guideline');
    }
}
