<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCPeriods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_periods', function (Blueprint $table) {
            $table->bigIncrements('id_period')->comment('Primary key referring to periods table');
            $table->string('period')->comment('Specify the period textually');
            $table->smallInteger('lastDay')->default(0)->comment('Specify the days for the period');
            $table->smallInteger('lastMonth')->default(0)->comment('Specify the months for the period');
            $table->smallInteger('lastYear')->default(0)->comment('Specify the years for the period');
            $table->smallInteger('lastRealDay')->default(0)->comment('Specify the days for the reale end of the period');
            $table->smallInteger('lastRealMonth')->default(0)->comment('Specify the months for the reale end of the period');
            $table->smallInteger('lastRealYear')->default(0)->comment('Specify the years for the reale end of the period');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_periods');
    }
}
