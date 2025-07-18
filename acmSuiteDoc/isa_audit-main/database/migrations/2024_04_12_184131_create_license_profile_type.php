<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicenseProfileType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('license_profile_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_license');
            $table->unsignedBigInteger('id_profile_type');
            $table->integer('quantity');
            $table->foreign('id_license')->references('id')->on('licenses')->onDelete('cascade');
            $table->foreign('id_profile_type')->references('id_profile_type')->on('t_profile_types')->onDelete('cascade');
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
        Schema::dropIfExists('license_profile_type');
    }
}
