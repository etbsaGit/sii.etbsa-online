<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->integer('kilometraje_servicio');
            $table->text('motivo');
            $table->string('con_cargo_a');
            $table->string('file')->nullable();
            $table->foreignId('estatus_id')->default(1);
            $table->foreignId('vehicle_id');
            $table->foreignId('suc_cargo');
            $table->foreignId('solicitante');
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
        Schema::dropIfExists('vehicle_services');
    }
}
