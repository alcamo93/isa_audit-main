<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_people', function (Blueprint $table) {
            $table->bigIncrements('id_person')->comment('Primary key referring to people table');
            $table->string('first_name', 255)->comment('Specifies the name(s)');
            $table->string('second_name', 255)->comment('Specifies the paternal last name');
            $table->string('last_name', 255)->nullable()->default('')->comment('Specifies the maternal last name');
            $table->string('rfc', 13)->nullable()->unique()->comment('RFC');
            $table->string('gender', 20)->nullable()->comment('Gender');
            $table->string('phone', 10)->nullable()->comment('Phone number');
            $table->timestampTz('birthdate')->nullable()->comment('Birthdate');
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
        Schema::dropIfExists('t_people');
    }
}
