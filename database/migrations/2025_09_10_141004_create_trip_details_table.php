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
            $table->Date("start_date");
            $table->Date("end_date")->nullable();
            $table->String("from_destination")->nullable();
            $table->String("to_destination")->nullable();
            $table->String("material")->nullable();
            $table->Integer("total_bags")->nullable();
            $table->Double("loading_labour")->nullable();
            $table->Double("unloading_labour")->nullable();
            $table->Double("rent")->nullable();
            $table->Double("advance")->nullable();
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
