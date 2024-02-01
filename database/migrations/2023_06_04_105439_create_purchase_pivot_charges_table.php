<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasePivotChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_pivot_charges', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreignId('agency_id')->references('id')->on('agencies');
            $table->foreignId('department_id')->references('id')->on('departments');

            $table->double('percent')->default(0);

            $table->primary([
                'purchase_order_id',
                'agency_id',
                'department_id'
            ], 'purchase_pivot_charges_primary');

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
        Schema::dropIfExists('purchase_pivot_charges');
    }
}