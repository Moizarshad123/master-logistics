<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_payments', function (Blueprint $table) {
            $table->id();
            $table->Integer("trip_id");
            $table->Integer("driver_id");
            $table->String("payment_type")->default("Cash");
            $table->double("amount")->default(0);
            $table->date("date")->nullable();
            $table->text("comments")->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('trip_payments');
    }
}
