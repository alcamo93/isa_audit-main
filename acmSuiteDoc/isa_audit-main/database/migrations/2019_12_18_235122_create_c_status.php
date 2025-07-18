<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_status', function (Blueprint $table) {
            $table->bigIncrements('id_status')->comment('Primary key referring to status table');
            $table->string('status')->comment('Status name');
            $table->integer('group')->comment('Status group');
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
        Schema::dropIfExists('c_status');
    }
}
