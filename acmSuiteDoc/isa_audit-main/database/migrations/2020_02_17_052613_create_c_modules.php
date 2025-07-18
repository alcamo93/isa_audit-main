<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_modules', function (Blueprint $table) {
            $table->bigIncrements('id_module')->comment('Primary key referring to modules table');
            $table->string('name_module', 255)->comment('Name module');
            $table->string('description', 255)->comment('Optional description for the module');
            $table->string('path', 255)->nullable()->default(null)->comment('Defined path');
            $table->string('icon_module', 25)->nullable()->comment('Icon class name');
            $table->string('color_module', 25)->nullable()->comment('Color class name');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->integer('sequence')->comment('Ordering sequence');
            $table->tinyInteger('has_submodule')->default(0)->comment('the value 1 refers to having a submodule, 0 has no submodule');
            $table->tinyInteger('owner')->default(0)->comment('The value 1 refers to the owner, 0 to the customer');
            $table->timestamps();
        });
        Schema::table('c_modules', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_modules');
    }
}
