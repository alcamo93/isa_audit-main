<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCRequirementTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_requirement_types', function (Blueprint $table) {
            $table->bigIncrements('id_requirement_type')->comment('Primary key referring to requirements_type table');
            $table->string('requirement_type')->comment('Specify the requirements_type textually');
            $table->integer('group')->comment('group 0 requriements | group 1 federal subrequirements | group 2 state subrequirements | group 4 local subrequirements | group 3 no use');
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
        Schema::dropIfExists('c_requirement_types');
    }
}
