<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsHistoricalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_historical', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gps_id');
            $table->string('name');
            $table->unsignedInteger('uploaded_by');

            $table->unsignedInteger('gps_group_id')->nullable();
            $table->unsignedInteger('gps_chip_id')->nullable();

            $table->enum('currency', ['MXN', 'USD'])->default('MXN');
            $table->double('exchange_rate', 12, 2)->default(1);
            $table->double('amount', 12, 2)->default(0);
            $table->string('invoice')->nullable();
            $table->string('payment_type')->nullable();

            $table->timestamp('installation_date')->nullable();
            $table->timestamp('renew_date')->nullable();
            $table->timestamp('cancellation_date')->nullable();

            $table->text('description')->nullable();
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
        Schema::dropIfExists('gps_historical');
    }
}
