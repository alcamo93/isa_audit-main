<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRAuditMatters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_audit_matters', function (Blueprint $table) {
            $table->bigIncrements('id_audit_matter');
            $table->double('total')->default(0)->comment('Specify percentage compliance per matter');
            $table->tinyInteger('self_audit')->default(1)->comment('The value 1 refers to the is in charge of the audit, 0 to the ISA Ambiental');
            $table->unsignedBigInteger('id_audit_register')->comment('Foreign key referring to audit registers table');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_matter')->comment('Foreign key referring to matters table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('r_audit_matters', function($table) {
            $table->foreign('id_audit_register')->references('id_audit_register')->on('t_audit_registers')->onDelete('cascade');
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
            $table->foreign('id_matter')->references('id_matter')->on('c_matters')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('r_audit_matters');
    }
}
