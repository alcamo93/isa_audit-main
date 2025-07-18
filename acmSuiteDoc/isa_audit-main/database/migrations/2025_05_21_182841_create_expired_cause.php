<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpiredCause extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expired_cause', function (Blueprint $table) {
            $table->id();
            $table->string('cause', 900);
            $table->dateTime('original_date');
            $table->dateTime('extension_date');
            $table->unsignedBigInteger('expiredable_id');
			$table->string('expiredable_type');
			$table->index(['expiredable_id', 'expiredable_type']);
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
        Schema::dropIfExists('expired_cause');
    }
}
