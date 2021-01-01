<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_type');
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->decimal('unit_price',10,2);
            $table->decimal('weight',8,3)->nullable()->comment('kg');
            $table->decimal('volume',8,3)->nullable()->comment('l');
            $table->integer('pack_size')->nullable();
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
        Schema::dropIfExists('items');
    }
}
