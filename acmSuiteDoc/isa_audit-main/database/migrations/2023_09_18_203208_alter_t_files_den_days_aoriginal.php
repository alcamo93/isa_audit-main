<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTFilesDenDaysAoriginal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->tinyInteger('is_original')->default(1)->after('has_file')->comment('The value 1 is original, 0 is copy');
            $table->integer('days')->after('end_date')->nullable();
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
            $table->dropColumn('is_original');
            $table->dropColumn('days');
        });
    }
}
