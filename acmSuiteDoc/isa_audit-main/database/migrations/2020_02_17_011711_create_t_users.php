<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_users', function (Blueprint $table) {
            $table->bigIncrements('id_user')->comment('Primary key referring to users table');
            $table->string('email')->unique()->comment('Email used like user');
            $table->string('password')->comment('Encrypted password');
            $table->string('secondary_email')->nullable()->unique()->comment('Email used only password notificatión');
            $table->string('picture', 200)->nullable()->default('default.png')->comment('Profile photography');
            $table->unsignedBigInteger('id_customer')->comment('Foreign key referring to customers table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->unsignedBigInteger('id_person')->comment('Foreign key referring to people table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_profile')->comment('Foreign key referring to profile table');
            $table->rememberToken()->comment('Token generator');
            $table->timestamps();
        });
        Schema::table('t_users', function($table) {
            $table->foreign('id_customer')->references('id_customer')->on('t_customers')->onDelete('restrict');
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_profile')->references('id_profile')->on('t_profiles')->onDelete('restrict');
            $table->foreign('id_person')->references('id_person')->on('t_people')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_users');
    }
}
