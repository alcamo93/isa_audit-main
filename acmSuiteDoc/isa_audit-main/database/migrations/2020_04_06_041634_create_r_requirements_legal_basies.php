<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRRequirementsLegalBasies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_requirements_legal_basies', function (Blueprint $table) {
            $table->bigIncrements('id_requirement_lb');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requerimients table');
            $table->unsignedBigInteger('id_legal_basis')->comment('Foreign key referring to legal basises table');
            $table->timestamps();
        });
        Schema::table('r_requirements_legal_basies', function($table) {
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('cascade');
            $table->foreign('id_legal_basis')->references('id_legal_basis')->on('t_legal_basises')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_requirements_legal_basies');
    }
}
