<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCommentsAddMorph extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('t_comments', function (Blueprint $table) {
			$table->unsignedBigInteger('commentable_id')->after('id_user')->nullable();
			$table->string('commentable_type')->after('commentable_id')->nullable();
			$table->index(['commentable_id', 'commentable_type']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('t_comments', function (Blueprint $table) {
			$table->dropColumn('commentable_type');  
			$table->dropColumn('commentable_id');
		});
	}
}
