<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSweetnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_sweetners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->double('total_litres');
            $table->double('per_litre_price');
            $table->string('fuel_type');
            $table->double('total_amount', 15, 2);
            $table->date('date')->nullable();
            $table->string('receiving_receipt')->nullable();
            $table->string('delivery_challan')->nullable();
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
        Schema::dropIfExists('master_sweetners');
    }
}
