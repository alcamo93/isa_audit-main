<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTFilesDenAddFileSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->integer('file_size')->nullable()->default(0)->after('url')->comment('Size in bytes');
            $table->string('file_type')->nullable()->after('file_size')->comment('Type file');
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
            $table->dropColumn('file_size');
            $table->dropColumn('file_type');
        });
    }
}
