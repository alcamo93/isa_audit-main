<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTSubrequirements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_subrequirements', function (Blueprint $table) {
            $table->bigIncrements('id_subrequirement')->comment('Primary key referring to subrequirements table');
            $table->string('no_subrequirement')->comment('Specify the keyword to the requirement');
            $table->text('subrequirement')->comment('Specify the requirement requested in the audit');
            $table->text('description')->nullable()->comment('Specify the description requirement requested in the audit');
            $table->longText('help_subrequirement')->nullable()->comment('Auditors help');
            $table->text('acceptance')->nullable()->comment('Acceptance criterion');
            $table->double('order')->comment('Specify the aspect order');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requerimients table');
            $table->unsignedBigInteger('id_matter')->comment('Foreign key referring to matters table');
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_audit_type')->comment('Foreign key referring to audit types table');
            $table->unsignedBigInteger('id_obtaining_period')->comment('Foreign key referring to periods table, it refers to time left to obtain the requirement');
            $table->unsignedBigInteger('id_update_period')->nullable()->comment('Foreign key referring to periods table, it refers to time left to close the last requirement obtain and get a new one');
            $table->unsignedBigInteger('id_condition')->comment('Foreign key referring to conditions table');
            $table->unsignedBigInteger('id_requirement_type')->comment('Foreign key referring to requiriment types table');
            $table->unsignedBigInteger('id_application_type')->comment('Foreign key referring to requiriment types table');
            $table->unsignedBigInteger('id_state')->nullable()->comment('Foreign key referring to states table');
            $table->timestamps();
        });
        Schema::table('t_subrequirements', function($table) {
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('cascade');
            $table->foreign('id_matter')->references('id_matter')->on('c_matters')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_audit_type')->references('id_audit_type')->on('c_audit_types')->onDelete('restrict');
            $table->foreign('id_obtaining_period')->references('id_period')->on('c_periods')->onDelete('restrict');
            $table->foreign('id_update_period')->references('id_period')->on('c_periods')->onDelete('restrict');
            $table->foreign('id_condition')->references('id_condition')->on('c_conditions')->onDelete('restrict');
            $table->foreign('id_requirement_type')->references('id_requirement_type')->on('c_requirement_types')->onDelete('restrict');
            $table->foreign('id_application_type')->references('id_application_type')->on('c_application_types')->onDelete('restrict');
            $table->foreign('id_state')->references('id_state')->on('c_states')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_subrequirements');
    }
}
