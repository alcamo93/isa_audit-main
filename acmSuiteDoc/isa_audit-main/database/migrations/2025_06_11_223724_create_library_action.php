<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibraryAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('library_action', function (Blueprint $table) {
            $table->id();
            $table->string('action', 50);
            $table->text('data');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->unsignedBigInteger('library_id');
            $table->foreign('library_id')->references('id')->on('library')->onDelete('restrict');
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
        Schema::dropIfExists('library_action');
    }
}
