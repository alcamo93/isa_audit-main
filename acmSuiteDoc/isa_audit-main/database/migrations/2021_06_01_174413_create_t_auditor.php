<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAuditor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_auditor', function (Blueprint $table) {
            $table->bigIncrements('id_auditor');
            $table->unsignedBigInteger('id_user')->comment('Reference to user');
            $table->unsignedBigInteger('id_audit_processes')->comment('Reference to contract');
            $table->tinyInteger('leader')->comment('1 si es leader, 0 si no es leader');
            $table->timestamps();
        });
        Schema::table('t_auditor', function($table) {
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_audit_processes')->references('id_audit_processes')->on('t_audit_processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_auditor');
    }
}
