<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyIssuesSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_issues_suppliers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('item_type');
            $table->unsignedBigInteger('item_id');
            $table->decimal('current_units_price',10,2);
            $table->integer('number_of_units');
            $table->decimal('daily_value',12,2);
            $table->unsignedBigInteger('user_id')->comment('created user id');
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
        Schema::dropIfExists('daily_issues_suppliers');
    }
}
