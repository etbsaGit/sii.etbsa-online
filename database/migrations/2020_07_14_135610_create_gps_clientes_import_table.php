<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsClientesImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_clientes_import', function (Blueprint $table) {
            $table->id();
            $table->string('sim')->nullable();
            $table->string('nombre');
            $table->string('razon_social')->nullable();
            $table->string('rfc')->nullable();
            $table->string('gps');
            $table->string('sucursal')->nullable();
            $table->string('departamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_clientes_import');
    }
}
