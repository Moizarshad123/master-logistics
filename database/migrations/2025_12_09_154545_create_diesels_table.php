<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDieselsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diesels', function (Blueprint $table) {
            $table->id();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->enum('type', ['Diesel', 'Petrol', 'Mobil oil']);
            $table->date('date');
            $table->time('time');
            $table->decimal('litres', 10, 2);
            $table->decimal('per_liter_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('diesels');
    }
}
