<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRContractAspectsRemoveEvaluates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('r_contract_aspects', function (Blueprint $table) {
            $table->dropColumn('requirements_evaluation');
            $table->dropColumn('subrequirements_evaluation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('r_contract_aspects', function (Blueprint $table) {
            $table->json('requirements_evaluation')->nullable()->after('id_state')->comment('Set of Requirements ID on which the assessment of the type of application was based');
            $table->json('subrequirements_evaluation')->nullable()->after('requirements_evaluation')->comment('Set of Subrequirements ID on which the assessment of the type of application was based');

        });
    }
}
