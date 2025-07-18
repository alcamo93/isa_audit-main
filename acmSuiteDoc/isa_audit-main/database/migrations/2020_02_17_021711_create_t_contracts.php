<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_contracts', function (Blueprint $table) {
            $table->bigIncrements('id_contract')->comment('Primary key referring to contracts table');
            $table->string('contract', 50)->comment('Name or identifier to contracts');
            $table->timestampTz('start_date')->nullable()->comment('Date of employment');
            $table->timestampTz('end_date')->nullable()->comment('Date end of employment');
            $table->unsignedBigInteger('id_status')->comment('Foreign key referring to status table');
            $table->unsignedBigInteger('id_customer')->comment('Foreign key referring to customers table');
            $table->unsignedBigInteger('id_corporate')->comment('Foreign key referring to corporates table');
            $table->timestamps();
        });
        Schema::table('t_contracts', function($table) {
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
            $table->foreign('id_customer')->references('id_customer')->on('t_customers')->onDelete('restrict');
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_contracts');
    }
}
