<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTContacts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_contacts', function (Blueprint $table) {
            $table->bigIncrements('id_contact')->comment('Primary key referring to contacts table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->string('ct_email', 45)->comment('Contact email');
            $table->string('ct_phone_office',16)->nullable()->comment('Office phone');
            $table->string('ct_ext', 16)->nullable()->comment('Office phone extension');
            $table->string('ct_cell',16)->comment('Contact cell');
            $table->string('ct_first_name', 255)->comment('Specifies the name(s)');
            $table->string('ct_second_name', 255)->comment('Specifies the paternal last name');
            $table->string('ct_last_name', 255)->nullable()->comment('Specifies the maternal last name');
            $table->string('degree', 45)->comment('Specifies the contact degree');
            $table->timestamps();
        });
        Schema::table('t_contacts', function($table) {
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_contacts');
    }
}
