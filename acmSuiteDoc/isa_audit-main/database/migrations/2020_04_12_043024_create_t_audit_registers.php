<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAuditRegisters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_audit_registers', function (Blueprint $table) {
            $table->bigIncrements('id_audit_register');
            $table->double('total')->default(0)->comment('Specify percentage of global compliance');
            $table->unsignedBigInteger('id_contract')->comment('Foreign key referring to contracts table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_audit_registers', function($table) {
            $table->foreign('id_contract')->references('id_contract')->on('t_contracts')->onDelete('restrict');
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
        Schema::dropIfExists('t_audit_registers');
    }
}
