<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTCorporates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_corporates', function (Blueprint $table) {
            $table->bigIncrements('id_corporate')->comment('Primary key referring to corporates table');
            $table->string('corp_tradename', 100)->comment('Corporate tradename');
            $table->string('corp_trademark', 100)->comment('Corporate trademark');
            $table->string('rfc', 13)->comment('Corporate rfc key');
            $table->tinyInteger('type')->comment('The value 1 refers if is operational, 0 is new');
            $table->unsignedBigInteger('id_customer')->comment('Foreign key referring to customers table');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_industry')->comment('Foreign key referring to industries table');
            $table->timestamps();
        });
        Schema::table('t_corporates', function($table) {
            $table->foreign('id_customer')->references('id_customer')->on('t_customers')->onDelete('cascade');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_industry')->references('id_industry')->on('c_industries')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_corporates');
    }
}
