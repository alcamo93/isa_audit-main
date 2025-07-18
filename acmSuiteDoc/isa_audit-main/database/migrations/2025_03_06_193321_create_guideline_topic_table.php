<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuidelineTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guideline_topic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guideline_id')->comment('Foreign key referring to guidelines table');
            $table->foreignId('topic_id')->constrained()->onDelete('cascade');
            $table->timestamps();

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
        Schema::dropIfExists('guideline_topic');
    }
}
