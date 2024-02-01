<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('model');
            $table->string('year');
            $table->string('brand');
            $table->string('serie');
            $table->string('ticket_card');
            $table->enum('fuel', ['MAGNA', 'DIESEL'])->default('MAGNA');
            $table->integer('last_mileage')->nullable();
            $table->integer('actual_mileage')->nullable();
            $table->integer('max_lts_fuel')->nullable();
            $table->integer('fuel_odometer')->nullable();
            $table->integer('mileage_last_service')->nullable();
            $table->integer('mileage_range_service')->nullable();
            $table->foreignId('agency_id')->nullable();
            $table->foreignId('user_id')->nullable();
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
        Schema::dropIfExists('vehicle');
    }
}
