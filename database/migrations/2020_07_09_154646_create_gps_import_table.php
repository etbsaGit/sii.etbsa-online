<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_import', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('sim')->nullable();
            $table->enum('currency', ['MXN', 'USD'])->default('MXN');
            $table->double('exchange_rate', 12, 2)->default(1);
            $table->double('amount', 12, 2)->default(0);
            $table->string('invoice')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('creacion')->nullable();
            $table->timestamp('fecha_carga')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_import');
    }
}
