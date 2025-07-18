<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTActionRegistersMakePolymorphicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_action_registers', function (Blueprint $table) {
            $table->dropForeign(['audit_register_id']);
            $table->dropColumn('audit_register_id');
            $table->dropForeign(['obligation_register_id']);
            $table->dropColumn('obligation_register_id');
            $table->unsignedBigInteger('registerable_id')->after('id_audit_processes')->nullable();
            $table->string('registerable_type')->after('registerable_id')->nullable();
            $table->index(['registerable_id', 'registerable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_action_registers', function (Blueprint $table) {
            $table->unsignedBigInteger('audit_register_id')->after('id_audit_processes')->nullable();
            $table->foreign('audit_register_id')->references('id_audit_register')->on('t_audit_registers')->onDelete('cascade');
            $table->unsignedBigInteger('obligation_register_id')->after('audit_register_id')->nullable();
            $table->foreign('obligation_register_id')->references('id')->on('obligation_registers')->onDelete('cascade');
            $table->dropIndex('registerable_id');
            $table->dropIndex('registerable_type');
            $table->dropColumn('registerable_id');
            $table->dropColumn('registerable_type');
        });
    }
}
