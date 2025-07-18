<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCLegalClassification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_legal_classification', function (Blueprint $table) {
            $table->bigIncrements('id_legal_c')->comment('Primary key referring to legal classsification table');
            $table->string('legal_classification')->comment('Specify the legal classification textually');
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
        Schema::dropIfExists('c_legal_classification');
    }
}
