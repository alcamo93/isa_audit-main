<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTFilesDenAddUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->dropForeign(['id_comment']);
            $table->dropColumn('id_comment');
            $table->unique(['id_obligation']);
            $table->unique(['id_task']);
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
            $table->unsignedBigInteger('id_comment')->nullable()->after('id_user')->comment('Foreign key referring to comment table');
            $table->foreign('id_comment')->references('id_comment')->on('t_comments')->onDelete('cascade');
            $table->dropUnique(['id_obligation']);
            $table->dropUnique(['id_task']);
        });
    }
}
