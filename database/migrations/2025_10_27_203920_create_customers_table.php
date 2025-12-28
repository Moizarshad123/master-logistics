<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customers')) {

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->Integer('customer_head_id');
            $table->string('name');
            $table->timestamps();
        });
    }
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
