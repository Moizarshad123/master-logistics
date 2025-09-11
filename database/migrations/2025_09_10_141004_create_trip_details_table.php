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
            $table->Date("end_date");
            $table->String("from_destination");
            $table->String("to_destination");
            $table->String("material");
            $table->Integer("total_bags");
            $table->Double("loading_labour");
            $table->Double("unloading_labour");
            $table->Double("rent");
            $table->Double("advance");
            $table->String("weight");
            $table->String("status");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_details');
    }
}
