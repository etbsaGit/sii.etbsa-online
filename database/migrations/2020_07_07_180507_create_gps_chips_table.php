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
            $table->id();
            $table->string('sim')->unique();
            $table->string('cuenta')->nullable()->unique();
            $table->string('imei')->nullable()->unique();
            $table->double('costo', 12, 2)->default(0);
            $table->timestamps('fecha_activacion')->nullable();
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
