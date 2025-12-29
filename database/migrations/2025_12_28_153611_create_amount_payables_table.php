<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmountPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amount_payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->double('amount');
            $table->date('date')->nullable();
            $table->string('payment_via');
            $table->string('other_source')->nullable();
            $table->string('receipt')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('fuel_suppliers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amount_payables');
    }
}
