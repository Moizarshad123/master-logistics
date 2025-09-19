<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->double('salary')->default(0);
            $table->string('cnic_front')->nullable();
            $table->string('cnic_back')->nullable();
            $table->string('driving_license_front')->nullable();
            $table->string('driving_license_back')->nullable();
            $table->string('image')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
