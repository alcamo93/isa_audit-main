<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTGuidelinesTableAddObjectiveColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_guidelines', function (Blueprint $table) {
            $table->mediumText('objective')->nullable()->after('id_city');
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
            $table->dropColumn('objective');
        });
    }
}
