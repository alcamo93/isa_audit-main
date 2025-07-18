<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTGuidelinesAddDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_guidelines', function (Blueprint $table) {
            $table->dateTime('last_date')->after('initials_guideline')->nullable()->comment('Indicates the date of the last reform');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_guidelines', function (Blueprint $table) {
            $table->dropColumn('last_date');
        });
    }
}
