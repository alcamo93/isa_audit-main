<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_profiles', function (Blueprint $table) {
            $table->bigIncrements('id_profile')->comment('Primary key referring to profile table');
            $table->string('profile_name', 100)->comment('Profile name');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_profile_type')->comment('Foreign key referring to profile table');
            $table->unsignedBigInteger('id_customer')->comment('Foreign key referring to customers table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->timestamps();
        });
        Schema::table('t_profiles', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_profile_type')->references('id_profile_type')->on('t_profile_types')->onDelete('restrict');
            $table->foreign('id_customer')->references('id_customer')->on('t_customers')->onDelete('restrict');
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_profiles');
    }
}
