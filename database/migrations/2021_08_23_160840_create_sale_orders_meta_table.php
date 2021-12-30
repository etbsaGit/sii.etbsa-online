<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrdersMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_orders_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sale_order_id');
            $table->foreign('sale_order_id')->references('id')->on('sale_orders')->onDelete('cascade');

            $table->string('type')->default('null');
            $table->string('key')->index();
            $table->text('value');
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
        Schema::dropIfExists('sale_orders_meta');
    }
}
