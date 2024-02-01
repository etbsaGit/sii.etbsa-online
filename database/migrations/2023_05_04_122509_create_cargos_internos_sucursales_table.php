<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargosInternosSucursalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos_internos_sucursales', function (Blueprint $table) {
            $table->id();

            $table->double('percent')->default(0);

            $table->foreignId('cargo_interno_id');
            $table->foreignId('sucursal_id');
            $table->foreignId('departamento_id');

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
        Schema::dropIfExists('cargos_internos_sucursales');
    }
}
