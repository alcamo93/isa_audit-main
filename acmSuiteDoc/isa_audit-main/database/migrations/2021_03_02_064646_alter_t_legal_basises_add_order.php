<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTLegalBasisesAddOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_legal_basises', function (Blueprint $table) {
            $table->double('publish')->after('legal_quote')->nullable();
            $table->double('order')->after('publish')->nullable()->comment('Specify the aspect order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_legal_basises', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
