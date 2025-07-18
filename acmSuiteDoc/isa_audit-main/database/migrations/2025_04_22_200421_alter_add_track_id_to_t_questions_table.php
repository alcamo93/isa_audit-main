<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddTrackIdToTQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('id_track')->nullable()->after('id_question');
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
            $table->dropColumn('id_track');
        });
    }
}
