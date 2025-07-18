<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRObligationRequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_obligation_requirements', function (Blueprint $table) {
            $table->bigIncrements('id_obligation_requirement');
            $table->unsignedBigInteger('id_obligation')->comment('Foreign key referring to obligations table');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requerimients table');
            $table->timestamps();
        });
        Schema::table('r_obligation_requirements', function($table) {
            $table->foreign('id_obligation')->references('id_obligation')->on('t_obligations')->onDelete('cascade');
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_obligation_requirements');
    }
}
