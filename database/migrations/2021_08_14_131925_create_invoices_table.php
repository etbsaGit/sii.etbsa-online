<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('folio');
            $table->uuid('folio_fiscal')->unique();
            $table->date('invoice_date');
            $table->double('amount')->nullable();
            $table->date('date_to_payment')->nullable();
            $table->date('payment_date')->nullable();

            $table->integer('invoiceable_id');
            $table->string('inovoiceable_type');
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
        Schema::dropIfExists('invoices');
    }
}
