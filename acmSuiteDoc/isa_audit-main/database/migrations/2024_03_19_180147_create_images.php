<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('original_name', 500);
            $table->string('hash_name', 500);
            $table->string('store_version');
            $table->string('store_origin');
            $table->string('directory');
            $table->string('extension');
            $table->string('file_type');
            $table->integer('file_size');
            $table->string('usage')->nullable();
            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type');
            $table->index(['imageable_id', 'imageable_type']);
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
        Schema::dropIfExists('images');
    }
}
