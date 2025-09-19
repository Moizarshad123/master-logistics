<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripVehicleExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('trip_vehicle_expenses', function (Blueprint $table) {
            $table->id();
            $table->Integer("trip_id");
            $table->Integer("vehicle_id");
            $table->String("expense")->nullable();
            $table->double("amount")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_vehicle_expenses');
    }
}
