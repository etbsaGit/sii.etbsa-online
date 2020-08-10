<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsChipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_chips', function (Blueprint $table) {
            // $table->id();
            $table->string('sim');
            $table->string('cuenta')->nullable();
            $table->string('imei')->nullable();
            $table->double('costo', 12, 2)->default(2600);
            $table->timestamp('fecha_activacion')->nullable();
            $table->timestamp('fecha_renovacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();

            $table->text('descripcion')->nullable();
            $table->unsignedInteger('gps_id')->unique()->nullable();

            $table->primary('sim');

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
        Schema::dropIfExists('gps_chips');
    }
}
