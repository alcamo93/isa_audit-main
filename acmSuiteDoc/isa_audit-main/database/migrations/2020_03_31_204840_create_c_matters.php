<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCMatters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_matters', function (Blueprint $table) {
            $table->bigIncrements('id_matter')->comment('Primary key referring to matters table');
            $table->string('matter', 50)->comment('Specify the matter');
            $table->string('description', 255)->nullable()->comment('Optional description for the matter');
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
        Schema::dropIfExists('c_matters');
    }
}
