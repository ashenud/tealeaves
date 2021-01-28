<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCollectionSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_collection_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('number_of_units');
            $table->decimal('current_units_price',10,2);
            $table->decimal('delivery_cost_per_unit',4,2);
            $table->decimal('delivery_cost',8,2);
            $table->decimal('daily_amount',12,2)->comment('actual amount of the_supplier current day');
            $table->decimal('daily_value',12,2)->comment('value of the_supplier current day after reducing delivery cost');
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
        Schema::dropIfExists('daily_collection_suppliers');
    }
}
