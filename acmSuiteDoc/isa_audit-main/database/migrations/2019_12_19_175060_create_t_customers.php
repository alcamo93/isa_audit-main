<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_customers', function (Blueprint $table) {
            $table->bigIncrements('id_customer')->comment('Primary key referring to customers table');
            $table->string('cust_tradename', 100)->comment('Customer tradename');
            $table->string('cust_trademark', 100)->comment('Customer trademark');
            $table->string('logo', 200)->nullable()->default('default.png')->comment('Logo or image that represents the customer');
            $table->string('sm_logo', 200)->nullable()->default('default.png')->comment('Small logo or image that represents the customer');
            $table->string('lg_logo', 200)->nullable()->default('default.png')->comment('Large logo or image that represents the customer');
            $table->tinyInteger('owner')->default(0)->comment('The value 1 refers to the owner, 0 to the customer');
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
        Schema::dropIfExists('t_customers');
    }
}
