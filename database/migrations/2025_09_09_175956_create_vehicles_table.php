<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_no')->nullable();
            $table->string('chachis_no')->nullable();
            $table->string('engine_no')->nullable();
            $table->Integer('vehicle_type')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('image')->nullable();
            $table->string('route_permit_sindh')->nullable();
            $table->string('route_permit_sindh_expiry')->nullable();
            $table->string('route_permit_punjab')->nullable();
            $table->string('route_permit_punjab_expiry')->nullable();
            $table->string('fitness_certificate')->nullable();
            $table->string('fitness_certificate_expiry')->nullable();
            $table->string('insurance_certificate')->nullable();
            $table->string('insurance_certificate_expiry')->nullable();
            $table->string('tax_token')->nullable();
            $table->string('tax_token_expiry')->nullable();
            $table->string('vehicle_file')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
}
