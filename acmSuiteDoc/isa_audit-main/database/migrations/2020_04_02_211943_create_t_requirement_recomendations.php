<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRequirementRecomendations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_requirement_recomendations', function (Blueprint $table) {
            $table->bigIncrements('id_recomendation')->comment('Primary key referring to requirements table');
            $table->text('recomendation')->comment('Specify the requirement requested in the audit');
            $table->unsignedBigInteger('id_requirement')->nullable()->comment('Foreign key referring to requirements table');
            $table->timestamps();
        });
        Schema::table('t_requirement_recomendations', function($table) {
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
        Schema::dropIfExists('t_requirement_recomendations');
    }
}
