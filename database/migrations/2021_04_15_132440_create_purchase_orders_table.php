<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->text('concepts')->nullable();
            $table->double('subtotal')->default(0);
            $table->double('tax')->default(0);
            $table->boolean('check_tax')->default(true);
            $table->double('total')->default(0);
            $table->foreignId('supplier_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->foreignId('estatus_id')->default(5);
            $table->foreignId('agency_id')->nullable();
            $table->string('metodo_pago_id')->nullable();
            $table->string('uso_cfdi_id')->nullable();
            $table->string('forma_pago_id')->nullable();
            $table->date('deliver_date')->nullable();
            $table->string('payment_condition')->nullable();
            $table->text('reason')->nullable();
            $table->string('authorization_token')->nullable()->unique();
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
        Schema::dropIfExists('purchase_orders');
    }
}
