<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTNews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_news', function (Blueprint $table) {
            $table->bigIncrements('id_new')->comment('Primary key referring to news table');
            $table->string('title')->comment('News title');
            $table->string('description', 500)->comment('News description');
            $table->dateTime('start_date')->comment('The beginning day of the news, it starts to show at this date');
            $table->dateTime('clear_date')->comment('The last day of the news, it stops showing at this date');
            $table->string('name_image')->comment('News image');
            $table->integer('show_image')->nullable()->comment('Show image on the dashboard news');
            $table->integer('show_title')->nullable()->comment('Show title on the dashboard news');
            $table->integer('show_description')->nullable()->comment('Show description on the dashboard news');
            $table->unsignedBigInteger('id_user')->comment('Foreign key referring to users table');
            $table->unsignedBigInteger('id_status')->nullable()->comment('Foreign key referring to status table');
            $table->timestamps();
        });
        Schema::table('t_news', function($table) {
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_status')->references('id_status')->on('c_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_news');
    }
}
