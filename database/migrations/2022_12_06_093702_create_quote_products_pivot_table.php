<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteProductsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_products_pivot', function (Blueprint $table) {
            $table->foreignId('quote_id');
            $table->foreignId('product_id');
            $table->double('price_unit');
            $table->integer('quantity');
            $table->string('currency');
            $table->double('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_products_pivot');
    }
}
