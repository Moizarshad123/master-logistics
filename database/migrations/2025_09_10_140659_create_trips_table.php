<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->String("trip_no");
            $table->Integer("trip_type");
            $table->date("trip_date");  
            $table->date("trip_end_date");  
            $table->decimal("balance");  
            $table->decimal("total_expense");        
            $table->decimal("remaining_balance");        
            $table->decimal("total_rent");        
            $table->Integer("vehicle_id");
            $table->Integer("driver_id");
            $table->String("status", 50);

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
        Schema::dropIfExists('trips');
    }
}
