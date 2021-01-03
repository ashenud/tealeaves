<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_collections', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('collection_no')->nullable();
            $table->decimal('daily_total_value',14,2);
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('edited_status')->default(0)->comment('0-not edited, 1-edited');
            $table->tinyInteger('confirm_status')->default(0)->comment('0-not confirmed, 1-confirmed');
            $table->softDeletes();
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
        Schema::dropIfExists('daily_collections');
    }
}
