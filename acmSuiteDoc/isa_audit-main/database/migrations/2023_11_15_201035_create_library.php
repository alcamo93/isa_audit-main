<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibrary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('library', function (Blueprint $table) {
            $table->id();
            $table->string('name', 500);
            $table->dateTime('init_date');
            $table->dateTime('end_date')->nullable();
            $table->dateTime('load_date');
            $table->tinyInteger('need_renewal')->nullable()->default(0);
            $table->tinyInteger('for_review')->nullable()->default(0);
            $table->tinyInteger('has_end_date')->nullable()->default(0);
            $table->unsignedInteger('days')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_category');
            $table->unsignedBigInteger('id_evaluate_requirement');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_category')->references('id_category')->on('c_categories')->onDelete('restrict');
            $table->foreign('id_evaluate_requirement')->references('id_evaluate_requirement')->on('t_evaluate_requirement')->onDelete('cascade');
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
        Schema::dropIfExists('library');
    }
}
