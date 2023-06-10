<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasePivotDetailProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_pivot_detail_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id');
            $table->string('c_ClaveProdServ');
            $table->text('description')->nullable();
            $table->integer('qty')->default(1);
            $table->integer('unit_id')->nullable();
            $table->double('price')->default(0);
            $table->double('discount')->default(0);
            $table->double('subtotal')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_pivot_detail_products');
    }
}
