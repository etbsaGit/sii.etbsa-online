<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleDispersalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_dispersals', function (Blueprint $table) {
            $table->id();
            $table->integer('gas_lts');
            $table->integer('kilometraje_actual');
            $table->double('odometro_percent_gas')->nullable();
            $table->string('destino')->nullable();
            $table->text('motivo');
            $table->string('con_cargo_a');
            $table->string('tarjeta');
            $table->string('photo')->nullable();
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
        Schema::dropIfExists('vehicle_dispersals');
    }
}
