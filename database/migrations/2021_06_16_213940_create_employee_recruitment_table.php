<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeRecruitmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_recruitment', function (Blueprint $table) {
            $table->id();
            $table->string('puesto');
            $table->string('sucursal_solcitante');
            $table->string('area_solcitante');

            $table->string('tipo_vacante')->default('nueva_creacion');
            $table->text('motivo_vacante');
            $table->string('especificacion_vacante')->nullable();

            $table->text('sexo_puesto');
            $table->text('escolaridad_puesto');
            $table->text('rango_edad_puesto');
            $table->string('idiomas_puesto')->nullable();
            $table->boolean('manejo_puesto')->default(false);
            $table->boolean('comision_puesto')->default(false);
            $table->integer('sueldo_puesto')->default(0);
            $table->text('habilidades_puesto');

            $table->text('competencias');

            $table->string('areas_experiencia');
            $table->string('tiempo_experiencia');
            $table->text('actividades_experiencia');

            $table->text('equipo_proporcionar')->nullable();

            $table->unsignedInteger('created_by');
            $table->unsignedInteger('estatus_id');

            $table->date('fecha_limite')->nullable()->nullable();
            $table->date('fecha_cubrio_vacante')->nullable();

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
        Schema::dropIfExists('employee_recruitment');
    }
}
