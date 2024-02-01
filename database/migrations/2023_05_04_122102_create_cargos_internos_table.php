<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargosInternosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargos_internos', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('num_economico')->nullable();
            $table->string('num_cliente')->nullable();
            $table->string('nip')->nullable();
            $table->string('concepto_cargo')->nullable();
            $table->string('ot')->nullable();

            $table->double('mano_obra')->nullable();
            $table->double('refacciones')->nullable();
            $table->double('kilometraje')->nullable();
            $table->double('foraneo')->nullable();
            $table->double('amount')->default(0);

            $table->foreignId('estatus_id')->nullable();
            $table->foreignId('created_by');
            $table->foreignId('autorized_by')->nullable();

            $table->timestamp('applied_at')->nullable();

            $table->timestamp('schedule_date')->nullable();
            $table->timestamp('authorization_date')->nullable();

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
        Schema::dropIfExists('cargos_internos');
    }
}