<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingProspectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_prospect', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('descripcion_topic');
            $table->foreignId('registered_by');
            $table->foreignId('assigned_by');
            $table->foreignId('assigned_to')->nullale();
            $table->foreignId('attended_by')->nullable();
            $table->foreignId('prospect_id');
            $table->foreignId('estatus_id');
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
        Schema::dropIfExists('tracking_prospect');
    }
}
