<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmsComparativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ams_comparatives', function (Blueprint $table) {
            $table->id();
            $table->string('customer_fullname');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('customer_city');
            $table->string('customer_country');
            $table->string('customer_company')->nullable();

            $table->text('equipments');

            $table->double("grooves", 8, 2);
            $table->double("tractor_value", 8, 2);
            $table->double("tractor_potencia", 8, 2);
            $table->double("diesel_prepare", 8, 2);
            $table->double("diesel_work", 8, 2);
            $table->double("diesel_price", 8, 2);
            $table->double("hectares", 8, 2);
            $table->double("cycles", 8, 2);
            $table->double("ams_value", 8, 2);

            $table->foreignId('created_by');
            $table->foreignId('updated_by');

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
        Schema::dropIfExists('comparatives');
    }
}
