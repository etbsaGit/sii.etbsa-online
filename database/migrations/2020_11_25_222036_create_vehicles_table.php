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
            $table->string('matricula')->unique()->nullable();
            $table->string('modelo')->nullable();
            $table->string('marca')->nullable();
            $table->string('serie')->unique()->nullable();
            $table->string('ticket_card')->unique()->nullable();
            $table->string('tipo_combustible');
            $table->integer('ultimo_kilometraje')->nullable();
            $table->integer('capacidad_tanque')->nullable();

            $table->foreignId("sucursal")->nullable();
            $table->foreignId('responsable')->nullable();
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
        Schema::dropIfExists('vehicles');
    }
}
