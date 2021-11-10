<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panic', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ledger_id');
            $table->foreign('ledger_id')->references('id')->on('ledger');
            $table->unsignedInteger('request_id')->nullable($value = true);
            $table->foreign('request_id')->references('id')->on('request');
            $table->unsignedInteger('admin_id')->nullable($value = true);
            $table->foreign('admin_id')->references('id')->on('admin');
            $table->text('history')->nullable();
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
        Schema::dropIfExists('panic');
    }
}
