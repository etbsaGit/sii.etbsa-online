<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('location')->references('id')->on('agencies');
            $table->foreignId('assigned_branch')->references('id')->on('agencies')->nullable();

            $table->string('model')->nullable();
            $table->string('num_serie')->nullable()->unique();
            $table->string('num_serie_motor')->nullable();
            $table->string('num_economico')->nullable()->unique();
            $table->double('costo_jd')->default(0);
            $table->double('costo_etbsa')->default(0);

            $table->string('invoice')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('arrival_date')->nullable();
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
        Schema::dropIfExists('product_inventories');
    }
}