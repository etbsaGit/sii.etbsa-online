<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderProductsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_products_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id');
            $table->string('c_ClaveProdServ');
            $table->text('description');
            $table->integer('qty')->default(1);
            $table->integer('unit_id');
            $table->double('price')->default(0);
            $table->double('discount')->default(0);
            $table->double('subtotal')->default(0);
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
        Schema::dropIfExists('purchase_order_products_pivot');
    }
}