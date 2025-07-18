<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTFilesDenAddCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->unsignedBigInteger('id_customer')->nullable()->after('id_audit_processes')->comment('Foreign key referring to customer table');
            $table->unsignedBigInteger('id_corporate')->nullable()->after('id_audit_processes')->comment('Foreign key referring to corporate table');
        });
        Schema::table('t_files_den', function ($table) {
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
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->dropForeign(['id_customer']);
            $table->dropForeign(['id_corporate']);
            $table->dropColumn('id_customer');
            $table->dropColumn('id_corporate');
        });
    }
}
