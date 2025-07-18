<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlterTFilesDenAddIdCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->unsignedBigInteger('id_category')->nullable()->after('id_user')->comment('Foreign key referring to audit processes table');
            $table->unsignedBigInteger('id_source')->nullable()->after('id_user')->comment('Foreign key referring to audit processes table');
            $table->unsignedBigInteger('id_audit_processes')->nullable()->after('id_user')->comment('Foreign key referring to audit processes table');
        });

        Schema::table('t_files_den', function (Blueprint $table) {
            $table->foreign('id_category')->references('id_category')->on('c_categories')->onDelete('restrict');
            $table->foreign('id_source')->references('id_source')->on('c_sources')->onDelete('restrict');
            $table->foreign('id_audit_processes')->references('id_audit_processes')->on('t_audit_processes')->onDelete('cascade');
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
            $table->dropForeign(['id_category']);
            $table->dropColumn('id_category');
            $table->dropForeign(['id_source']);
            $table->dropColumn('id_source');
            $table->dropForeign(['id_audit_processes']);
            $table->dropColumn('id_audit_processes');
        });
    }
}
