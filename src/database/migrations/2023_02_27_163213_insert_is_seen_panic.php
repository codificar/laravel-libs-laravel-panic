<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class InsertIsSeenPanic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('panic', function (Blueprint $table) {
            if (!Schema::hasColumn('panic', 'is_seen')) {
                $table->boolean('is_seen')->default(false)->nullable();
            }
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('panic', function (Blueprint $table) {
            if (Schema::hasColumn('panic', 'is_seen')) {
                $table->dropColumn('is_seen');
            }
        });
    }
}
