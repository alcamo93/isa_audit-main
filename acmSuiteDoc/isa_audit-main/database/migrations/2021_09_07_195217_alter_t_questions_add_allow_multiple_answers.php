<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTQuestionsAddAllowMultipleAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_questions', function (Blueprint $table) {
            $table->tinyInteger('allow_multiple_answers')->default(1)->after('order')->comment('The value 1 refers if is allow select multiple answer, 0 it not allow');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_questions', function (Blueprint $table) {
            $table->dropColumn('allow_multiple_answers');
        });
    }
}
