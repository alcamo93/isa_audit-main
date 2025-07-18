<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCRequirementTypesAddIdentification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('c_requirement_types', function (Blueprint $table) {
            $table->integer('identification')->default(0)->after('group')->comment('It is No identification 0 requriements/subrequriements | It is identification 1 0 requriements/subrequriements');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('c_requirement_types', function (Blueprint $table) {
            $table->dropColumn('identification');
        });
    }
}
