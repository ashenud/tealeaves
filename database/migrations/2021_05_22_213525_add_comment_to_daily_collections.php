<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentToDailyCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_collections', function (Blueprint $table) {
            $table->decimal('daily_total_value',14,2)->comment('These values are not correct')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_collections', function (Blueprint $table) {
            $table->decimal('daily_total_value',14,2)->comment('')->change();
        });
    }
}
