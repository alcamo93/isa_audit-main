<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSubrequirementRecomendations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_subrequirement_recomendations', function (Blueprint $table) {
            $table->bigIncrements('id_recomendation')->comment('Primary key referring to requirements table');
            $table->text('recomendation')->comment('Specify the requirement requested in the audit');
            $table->unsignedBigInteger('id_subrequirement')->nullable()->comment('Foreign key referring to subrequirements table');
            $table->timestamps();
        });
        Schema::table('t_subrequirement_recomendations', function($table) {
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_subrequirement_recomendations');
    }
}
