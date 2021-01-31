<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthEndSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_end_suppliers', function (Blueprint $table) {            
            $table->id();
            $table->unsignedBigInteger('month_end_id');
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('total_earnings',14,2);
            $table->decimal('total_cost',14,2);
            $table->decimal('total_installment',14,2);
            $table->decimal('current credit',14,2);
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
        Schema::dropIfExists('month_end_suppliers');
    }
}
