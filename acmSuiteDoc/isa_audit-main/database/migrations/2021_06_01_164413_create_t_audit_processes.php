<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAuditProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_audit_processes', function (Blueprint $table) {
            $table->bigIncrements('id_audit_processes');
            $table->string('audit_processes', 100)->comment('Name of audit processes');
            $table->string('specification_scope', 100)->nullable()->comment('Fill in this field if scope is departament (id_scope 2)');
            $table->tinyInteger('evaluate_risk')->nullable()->default(0)->comment('The value 1 refers to the affirmative, 0 negative');
            $table->unsignedBigInteger('id_corporate')->comment('Reference to corporate');
            $table->unsignedBigInteger('id_customer')->comment('Reference to customer');
            $table->unsignedBigInteger('id_scope')->comment('Reference to scope');
            $table->timestamps();
        });
        Schema::table('t_audit_processes', function($table) {
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('restrict');
            $table->foreign('id_customer')->references('id_customer')->on('t_customers')->onDelete('restrict');
            $table->foreign('id_scope')->references('id_scope')->on('c_scope')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_audit_processes');
    }
}
