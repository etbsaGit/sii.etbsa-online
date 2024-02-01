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
            $table->boolean('drum_dispersion')->default(false);
            $table->integer('fuel_odometer')->nullable();
            $table->integer('mileage_last')->nullable();
            $table->integer('mileage_actual')->nullable();

            $table->string('reason_dispersal');
            $table->text('reason_data')->nullable();
            $table->text('reason_note')->nullable();

            $table->string('fuel_name');
            $table->double('fuel_liters');
            $table->double('fuel_cost_liter');
            $table->date('date_to_disperse');

            $table->text('tickets_info')->nullable();

            $table->foreignId('agency_id');
            $table->foreignId('department_id');
            $table->foreignId('estatus_id');
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
        Schema::dropIfExists('vehicle_dispersal');
    }
}
