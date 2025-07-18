<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTAnswersQuestionAddOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_answers_question', function (Blueprint $table) {
            $table->integer('order')->nullable()->after('description')->comment('Specify the answer order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_answers_question', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
