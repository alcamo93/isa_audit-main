<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTGuidelines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_guidelines', function (Blueprint $table) {
            $table->bigIncrements('id_guideline')->comment('Primary key referring to guidelines table');
            $table->string('guideline',800)->comment('Name guidelines');
            $table->string('initials_guideline',250)->comment('Initials guidelines');
            $table->unsignedBigInteger('id_application_type')->comment('Foreign key referring to application type table');
            $table->unsignedBigInteger('id_legal_c')->comment('Foreign key referring to legal classification table');
            $table->unsignedBigInteger('id_state')->nullable()->comment('Foreign key referring to legal classification table, if id_application_type is 2 set id_state');
            $table->timestamps();
        });
        Schema::table('t_guidelines', function($table) {
            $table->foreign('id_legal_c')->references('id_legal_c')->on('c_legal_classification')->onDelete('restrict');
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
        Schema::dropIfExists('t_guidelines');
    }
}
