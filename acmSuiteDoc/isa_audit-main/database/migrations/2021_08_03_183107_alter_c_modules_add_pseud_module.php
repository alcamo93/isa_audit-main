<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCModulesAddPseudModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('c_modules', function (Blueprint $table) {
            $table->string('pseud_module', 255)->after('name_module')->comment('Pseudonym module');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('c_modules', function (Blueprint $table) {
            $table->dropColumn('pseud_module');
        });
    }
}
