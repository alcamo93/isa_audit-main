<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCSubmodules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_submodules', function (Blueprint $table) {
            $table->bigIncrements('id_submodule')->comment('Primary key referring to submodules table');
            $table->string('name_submodule', 255)->comment('Name submodule');
            $table->string('initials_submodule', 255)->comment('Initials submodule');
            $table->string('description', 255)->nullable()->comment('Optional description for the submodule');
            $table->string('path', 255)->comment('Defined path');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_module')->comment('Foreign key referring to module table');
            $table->timestamps();
        });
        Schema::table('c_submodules', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_module')->references('id_module')->on('c_modules')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_submodules');
    }
}
