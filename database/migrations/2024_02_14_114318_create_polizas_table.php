<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolizasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polizas', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('reference_name')->nullable();
            $table->string('reference_concept')->nullable();
            $table->text('description')->nullable();
            $table->double('amount', '12,2')->default(0);
            $table->foreignId('currency_id')->references('id')->on('currency');
            
            $table->foreignId('payment_source_id')->nullable()->references('id')->on('payment_sources');
            $table->foreignId('tipo_poliza_id')->nullable()->references('id')->on('tipo_polizas');
            
            $table->foreignId('origen_bank_accounts_id')->nullable()->references('id')->on('agency_bank_accounts');
            $table->foreignId('apply_bank_accounts_id')->references('id')->on('agency_bank_accounts');
            
            $table->boolean('is_applied')->default(false);
            $table->dateTime('apply_date')->nullable();
            
            $table->foreignId('category_id')->nullable()->references('id')->on('cat_product_category');
            $table->foreignId('user_created')->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('user_updated')->nullable()->references('id')->on('users')->onUpdate('cascade');


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
        Schema::dropIfExists('polizas');
    }
}
