<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name', 500);
            $table->string('hash_name', 500);
            $table->string('store_version');
            $table->string('store_origin');
            $table->string('directory');
            $table->string('extension');
            $table->string('file_type');
            $table->integer('file_size');
            $table->tinyInteger('is_current')->nullable()->default(1);
            $table->integer('renewal_number')->nullable()->default(1);
            $table->dateTime('load_date');
            $table->dateTime('drop_date');
            $table->dateTime('init_date');
            $table->dateTime('end_date')->nullable();
            $table->unsignedBigInteger('library_id');
            $table->foreign('library_id')->references('id')->on('library')->onDelete('cascade');
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
        Schema::dropIfExists('files');
    }
}
