<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsWialonImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_wialon_import', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('grupo')->nullable();
            $table->string('sucursal')->nullable();
            $table->string('departamento')->nullable();
            $table->string('sim')->nullable();
            $table->string('uiid')->nullable();
            $table->timestamps('creacion')->nullable();
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
        Schema::dropIfExists('gps_wialon_import');
    }
}
