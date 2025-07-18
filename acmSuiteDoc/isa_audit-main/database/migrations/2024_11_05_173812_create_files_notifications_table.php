<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_id');
            $table->datetime('date');
            $table->boolean('done')->default(false);
            $table->timestamps();

            $table->foreign('library_id')->references('id')->on('library')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files_notifications');
    }
}
