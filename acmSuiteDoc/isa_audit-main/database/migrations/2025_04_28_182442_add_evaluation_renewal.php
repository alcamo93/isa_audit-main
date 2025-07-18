<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvaluationRenewal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_renewals', function (Blueprint $table) {
            $table->id();
            $table->boolean('keep_files');
            $table->boolean('keep_risk');
            $table->datetime('date');
            $table->unsignedBigInteger('id_audit_processes');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
            $table->foreign('id_audit_processes')->references('id_audit_processes')->on('t_audit_processes')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_renewals');
    }
}
