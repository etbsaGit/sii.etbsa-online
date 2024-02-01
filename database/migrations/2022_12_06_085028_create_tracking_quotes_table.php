<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_quotes', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_due');
            $table->foreignId('tracking_id');
            $table->foreignId('currency_id');
            $table->string('currency');
            $table->double('subtotal')->default(0);
            $table->double('tax')->default(0);
            $table->double('total')->default(0);
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
        Schema::dropIfExists('quote');
    }
}
