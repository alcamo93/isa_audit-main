<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->unsignedBigInteger('id_customer')->after('id_state')->nullable()->comment('Foreign key referring to customers table only for special requiremnts');
            $table->unsignedBigInteger('id_corporate')->after('id_state')->nullable()->comment('Foreign key referring to corporates table only for special requiremnts');
        });
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->foreign('id_customer')->references('id_customer')->on('t_customers')->onDelete('cascade'); 
            $table->foreign('id_corporate')->references('id_corporate')->on('t_corporates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_requirements', function (Blueprint $table) {
            $table->dropForeign(['id_customer']);
            $table->dropForeign(['id_corporate']);
            $table->dropColumn('id_customer');
            $table->dropColumn('id_corporate');
        });
    }
}
