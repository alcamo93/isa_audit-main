<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTLegalBasises extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_legal_basises', function (Blueprint $table) {
            $table->bigIncrements('id_legal_basis')->comment('Primary key referring to legal basises table');
            $table->string('legal_basis', 100)->comment('Specify the legal basis textually');
            $table->longText('legal_quote')->nullable()->comment('Transcription of the legal basis');
            $table->unsignedBigInteger('id_guideline')->comment('Foreign key referring to legal classification table');
            $table->unsignedBigInteger('id_application_type')->comment('Foreign key referring to matters table');
            $table->timestamps();
        });
        Schema::table('t_legal_basises', function($table) {
            $table->foreign('id_guideline')->references('id_guideline')->on('t_guidelines')->onDelete('cascade');
            $table->foreign('id_application_type')->references('id_application_type')->on('c_application_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_legal_basises');
    }
}
