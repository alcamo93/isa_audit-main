<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRSubrequirementsLegalBasies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_subrequirements_legal_basies', function (Blueprint $table) {
            $table->bigIncrements('id_subrequirements_lb');
            $table->unsignedBigInteger('id_subrequirement')->comment('Foreign key referring to subrequerimients table');
            $table->unsignedBigInteger('id_legal_basis')->comment('Foreign key referring to legal basises table');
            $table->timestamps();
        });
        Schema::table('r_subrequirements_legal_basies', function($table) {
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('cascade');
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
        Schema::dropIfExists('r_subrequirements_legal_basies');
    }
}
