<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Backups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->timestampTz('init_date')->nullable();
            $table->timestampTz('end_date')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_audit_processes');
            $table->foreign('id_user')->references('id_user')->on('t_users')->onDelete('restrict');
            $table->foreign('id_audit_processes')->references('id_audit_processes')->on('t_audit_processes')->onDelete('restrict');
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
        Schema::dropIfExists('backups');
    }
}
