<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFertilizerIssuesSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fertilizer_issues_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('fertilizer_issue_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('item_type');
            $table->unsignedBigInteger('item_id');
            $table->decimal('current_units_price',10,2);
            $table->integer('number_of_units');
            $table->decimal('daily_value',12,2);
            $table->integer('payment_frequency');
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
        Schema::dropIfExists('fertilizer_issues_suppliers');
    }
}
