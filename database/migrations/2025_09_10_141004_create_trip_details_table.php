<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('trip_details', function (Blueprint $table) {
            $table->id();
            $table->Integer("trip_id");
            $table->Integer("customer_id");
            $table->String("trip_type")->nullable();
            $table->Date("start_date");
            $table->Date("end_date")->nullable();
            $table->Integer("from_destination")->nullable();
            $table->Integer("to_destination")->nullable();
            $table->String("material")->nullable();
            $table->String("material_type")->nullable();

            
            $table->Integer("total_bags")->nullable();
            $table->Double("weekly_labour")->nullable();
            $table->Double("baloch_labour")->nullable();
            $table->Double("baloch_labour_rate")->nullable();
            $table->Double("advance")->nullable();

            

            
            
            $table->Double("rate")->nullable();
            $table->Double("no_of_labour")->nullable();

            $table->boolean("is_payment_receive")->nullable();
            $table->Double("receive_amount")->nullable();
            $table->Integer("receive_by")->nullable();


            
            $table->Double("rent")->nullable();
            // $table->Double("advance")->nullable();
            $table->Text("comments");
            $table->String("weight")->nullable();
            $table->String("status")->default("Started");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_details');
    }
}
