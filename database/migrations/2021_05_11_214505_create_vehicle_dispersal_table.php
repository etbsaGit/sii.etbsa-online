<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleDispersalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_dispersal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id');
            $table->foreignId('estatus_id');
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
            $table->integer('fuel_odometer');
            $table->integer('last_mileage');
            $table->integer('actual_mileage');
            $table->string('reason');
            $table->text('reason_description');
            $table->integer('fuel_lts');

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
        Schema::dropIfExists('vehicle_dispersal');
    }
}
