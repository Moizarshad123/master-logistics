<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_id');
            $table->integer('qty')->default(0);
            $table->double('unit_price')->default(0);
            $table->double('total_price')->default(0);
            $table->date('purchase_date');
            $table->String('vendor', 100)->nullable();
            $table->String('invoice_no', 50)->nullable();
            $table->integer('remaining_qty')->default(0);
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
        Schema::dropIfExists('inventories');
    }
}
