<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthEndsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_ends', function (Blueprint $table) {
            $table->id();            
            $table->string('month');
            $table->date('ended_date')->nullable();
            $table->unsignedBigInteger('user_id')->comment('ended user id')->nullable();
            $table->tinyInteger('ended_status')->default(0)->comment('0-not ended, 1-ended');
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
        Schema::dropIfExists('month_ends');
    }
}
