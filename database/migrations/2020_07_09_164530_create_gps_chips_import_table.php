<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsChipsImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_chips_import', function (Blueprint $table) {
            $table->id();
            $table->string('sim');
            $table->string('cuenta')->nullable();
            $table->string('imei')->nullable();
            $table->double('costo')->nullable();
            $table->timestamp('fecha_activacion')->nullable();
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
        Schema::dropIfExists('gps_chips_import');
    }
}
