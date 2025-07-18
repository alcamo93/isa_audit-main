<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterObligationRegisterAddEndDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obligation_registers', function (Blueprint $table) {
            $table->dateTime('end_date')->nullable()->after('init_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('obligation_registers', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
}
