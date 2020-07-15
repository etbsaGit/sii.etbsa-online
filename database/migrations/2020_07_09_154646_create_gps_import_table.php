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
            $table->string('grupo')->nullable();
            $table->string('sucursal')->nullable();
            $table->string('departamento')->nullable();
            $table->string('sim')->nullable();
            $table->string('uid')->nullable();
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
