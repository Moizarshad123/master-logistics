<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripVehicleExpensesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('trip_vehicle_expenses')) {
            Schema::create('trip_vehicle_expenses', function (Blueprint $table) {
                $table->id();
                $table->integer('trip_id');
                $table->integer('vehicle_id');
                $table->integer('expense_type_id');

                
                $table->string('expense')->nullable();
                $table->string('expense_from')->nullable();
                $table->double('amount', 15, 2)->nullable();
                $table->timestamps();
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('trip_vehicle_expenses');
    }
}
