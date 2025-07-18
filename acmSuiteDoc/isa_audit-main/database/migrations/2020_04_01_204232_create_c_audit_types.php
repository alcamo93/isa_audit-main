<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCAuditTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_audit_types', function (Blueprint $table) {
            $table->bigIncrements('id_audit_type')->comment('Primary key referring to audit types table');
            $table->string('audit_type')->comment('Specify the audit type textually');
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
        Schema::dropIfExists('c_audit_types');
    }
}
