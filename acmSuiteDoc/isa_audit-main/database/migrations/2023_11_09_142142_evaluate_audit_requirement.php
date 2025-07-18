<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EvaluateAuditRequirement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluate_audit_requirement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('complete')->default(0)->comment('The value 1 is complete, 0 no complete');
            $table->unsignedBigInteger('id_audit_aspect')->nullable()->comment('Foreign key referring to audit aspect');
            $table->unsignedBigInteger('id_requirement')->comment('Foreign key referring to requirement to evaluate');
            $table->unsignedBigInteger('id_subrequirement')->nullable()->comment('Foreign key referring to requirement to evaluate');
            $table->foreign('id_audit_aspect')->references('id_audit_aspect')->on('r_audit_aspects')->onDelete('cascade');
            $table->foreign('id_requirement')->references('id_requirement')->on('t_requirements')->onDelete('restrict');
            $table->foreign('id_subrequirement')->references('id_subrequirement')->on('t_subrequirements')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluate_audit_requirement');
    }
}
