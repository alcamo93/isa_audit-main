<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTProfilesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_profiles_permissions', function (Blueprint $table) {
            $table->bigIncrements('id_profile_permission')->comment('Primary key referring to profiles permissions table');
            $table->unsignedBigInteger('id_module')->comment('Foreign key referring to modules table');
            $table->unsignedBigInteger('id_submodule')->nullable()->comment('Foreign key referring to submodules table');
            $table->unsignedBigInteger('id_profile')->comment('Foreign key referring to profiles table');
            $table->integer('visualize')->default(0)->comment('Permission to view content');
            $table->integer('create')->default(0)->comment('Permission to create content');
            $table->integer('modify')->default(0)->comment('Permission to modify content');
            $table->integer('delete')->default(0)->comment('Permission to remove content');
            $table->timestamps();
        });
        Schema::table('t_profiles_permissions', function($table) {
            $table->foreign('id_module')->references('id_module')->on('c_modules')->onDelete('restrict');
            $table->foreign('id_submodule')->references('id_submodule')->on('c_submodules')->onDelete('restrict');
            $table->foreign('id_profile')->references('id_profile')->on('t_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_profiles_permissions');
    }
}
