<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsHistoricalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_historical', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gps_id');
            $table->string('sim')->nullable()->unique();
            $table->string('imei')->nullable();
            $table->double('cost',12,2)->default(0);
            $table->double('amount',12,2)->default(0);
            $table->timestamp('activation_date')->nullable();
            $table->timestamp('due_date')->nullable();
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
        Schema::dropIfExists('gps_historical');
    }
}
