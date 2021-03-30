<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActualEarningsColumnToMonthEndSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('month_end_suppliers', function (Blueprint $table) {            
            $table->decimal('total_earnings',14,2)->comment('toal ernings from tealeaves after deducting delevery cost')->change();
            $table->decimal('actual_earnings',14,2)->after('total_earnings')->nullable()->comment('toal ernings from tealeaves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('month_end_suppliers', function (Blueprint $table) {
            $table->dropColumn('actual_earnings');
        });
    }
}
