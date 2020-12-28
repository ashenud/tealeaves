<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('role_name');
            $table->tinyInteger('read')->comment('1-allowed, 0-not allowed');
            $table->tinyInteger('write')->comment('1-allowed, 0-not allowed');
            $table->tinyInteger('update')->comment('1-allowed, 0-not allowed');
            $table->tinyInteger('delete')->comment('1-allowed, 0-not allowed');
            $table->tinyInteger('usermanage')->comment('1-allowed, 0-not allowed');
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
        Schema::dropIfExists('roles');
    }
}
