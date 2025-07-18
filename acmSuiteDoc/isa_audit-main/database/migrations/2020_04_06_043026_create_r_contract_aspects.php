<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRContractAspects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_contract_aspects', function (Blueprint $table) {
            $table->bigIncrements('id_contract_aspect');
            $table->tinyInteger('self_audit')->default(1)->comment('The value 1 refers to the is in charge of the audit, 0 to the ISA Ambiental');
            $table->unsignedBigInteger('id_contract_matter')->comment('Foreign key referring to aplicability registers table');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_matter')->comment('Foreign key referring to matters table');
            $table->unsignedBigInteger('id_aspect')->comment('Foreign key referring to aspects table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_application_type')->nullable()->comment('Foreign key referring to application types table');
            $table->unsignedBigInteger('id_state')->comment('Foreign key referring to states table');
            $table->timestamps();
        });
        Schema::table('r_contract_aspects', function($table) {
            $table->foreign('id_contract_matter')->references('id_contract_matter')->on('r_contract_matters')->onDelete('cascade');
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_matter')->references('id_matter')->on('c_matters')->onDelete('restrict');
            $table->foreign('id_aspect')->references('id_aspect')->on('c_aspects')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
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
        Schema::dropIfExists('r_contract_aspects');
    }
}
