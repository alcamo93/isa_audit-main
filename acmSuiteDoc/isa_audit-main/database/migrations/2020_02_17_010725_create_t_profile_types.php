<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTProfileTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_profile_types', function (Blueprint $table) {
            $table->bigIncrements('id_profile_type')->comment('Primary key referring to profile types table');
            $table->string('type', 255)->nullable()->comment('Specified type or level in string');
            $table->tinyInteger('owner')->default(0)->comment('The value 1 refers to the owner, 0 to the customer');
            $table->tinyInteger('profile_level')->default(0)->comment('The value 1 refers to the owner, 3 to customer, 4 to corporative level, 5 to operative level');
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
        Schema::dropIfExists('t_profile_types');
    }
}