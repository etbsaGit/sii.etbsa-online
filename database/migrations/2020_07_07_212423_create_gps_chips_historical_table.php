<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsChipsHistoricalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_chips_historical', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gps_chip_id');
            $table->string('sim')->unique();
            $table->string('cuenta')->nullable()->unique();
            $table->string('imei')->nullable()->unique();
            $table->double('costo', 12, 2)->default(0);
            $table->timestamp('fecha_activacion')->nullable();
            $table->timestamp('fecha_renovacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();

            $table->text('descripcion')->nullable();
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
        Schema::dropIfExists('gps_chips_historical');
    }
}
