<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAudit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_audit', function (Blueprint $table) {
            $table->bigIncrements('id_audit');
            $table->tinyInteger('answer')->comment('The value 1 refers to the affirmative answer, 0 negative answer and 2 n/a answer');
            $table->text('finding')->nullable()->comment('Specify the finding in requirement requested in the audit');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requirements table');
            $table->unsignedBigInteger('id_subrequirement')->nullable()->comment('Foreign key referring to requirements table');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_recomendation')->nullable()->comment('Foreign key referring to requirement recomendations table');
            $table->unsignedBigInteger('id_subrecomendation')->nullable()->comment('Foreign key referring to subrequirement recomendations table');
            $table->timestamps();
        });
        Schema::table('t_audit', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('restrict');
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('restrict');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_recomendation')->references('id_recomendation')->on('t_requirement_recomendations')->onDelete('restrict');
            $table->foreign('id_subrecomendation')->references('id_recomendation')->on('t_subrequirement_recomendations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_audit');
    }
}
