<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estatus_id');
            $table->foreignId('tracking_id');
            $table->foreignId('customer_id');

            $table->double('amount');
            $table->foreignId('currency_id');

            $table->double('down_payment')->nullable();
            $table->string('payment_condition')->nullable();
            $table->string('term')->nullable();

            $table->foreignId('created_by');
            $table->foreignId('updated_by')->nullable();

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
        Schema::dropIfExists('sale_orders');
    }
}
