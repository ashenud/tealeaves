<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_issues', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('loan_no')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('amount',12,2);
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id')->comment('approved user id');
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
        Schema::dropIfExists('loan_issues');
    }
}
