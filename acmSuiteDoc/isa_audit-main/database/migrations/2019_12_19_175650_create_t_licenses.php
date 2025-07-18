<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTLicenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_licenses', function (Blueprint $table) {
            $table->bigIncrements('id_license')->comment('Primary key referring to licenses table');
            $table->string('license', 50)->comment('Name or identifier to licenses');
            $table->integer('usr_global')->commet('Number of users allowed for the global type');
            $table->integer('usr_corporate')->commet('Number of users allowed for the corporate type');
            $table->integer('usr_operative')->comment('Number of users allowed for the operative type');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_period')->comment('Foreign key referring to periods table');
            $table->timestamps();
        });
        Schema::table('t_licenses', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_period')->references('id_period')->on('c_periods')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_licenses');
    }
}
