<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTFilesDenAddColumnsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->tinyInteger('has_file')->after('file_type')->default(1)->nullable();
            $table->dateTime('load_date')->after('has_file')->nullable();
            $table->dateTime('drop_date')->after('load_date')->nullable();
            $table->dateTime('init_date')->after('drop_date')->nullable();
            $table->dateTime('end_date')->after('init_date')->nullable();
            $table->unsignedBigInteger('fileable_id')->after('end_date')->nullable();
            $table->string('fileable_type')->after('fileable_id')->nullable();
            $table->index(['fileable_id', 'fileable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_files_den', function (Blueprint $table) {
            $table->dropIndex('fileable_id');
            $table->dropIndex('fileable_type');
            $table->dropColumn('has_file');
            $table->dropColumn('load_date');
            $table->dropColumn('drop_date');
            $table->dropColumn('init_date');
            $table->dropColumn('end_date');
            $table->dropColumn('fileable_id');
            $table->dropColumn('fileable_type');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
