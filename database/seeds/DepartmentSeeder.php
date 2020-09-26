<?php

use App\Components\Common\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create(['title' => 'ADMINISTRACION']);
        Department::create(['title' => 'CONTABILIDAD']);
        Department::create(['title' => 'CREDITO Y COBRANZA']);
        Department::create(['title' => 'DIRECCION']);
        Department::create(['title' => 'LUBRICANTES']);
        Department::create(['title' => 'MARKETING']);
        Department::create(['title' => 'NUEVAS TECNOLOGIAS']);
        Department::create(['title' => 'POST VENTA']);
        Department::create(['title' => 'REFACCIONES']);
        Department::create(['title' => 'RIEGO']);
        Department::create(['title' => 'RRHH']);
        Department::create(['title' => 'SERVICIO AGRICOLA']);
        Department::create(['title' => 'SERVICIO INDRUSTRIAL']);
        Department::create(['title' => 'SISTEMAS']);
        Department::create(['title' => 'ASCACAMBARO']);
        Department::create(['title' => 'EQUIPO DEMO']);
        Department::create(['title' => 'GERENTE']);
        Department::create(['title' => 'BRIGADA VERDE']);
        Department::create(['title' => 'ASC QRO']);
        Department::create(['title' => 'TRASLADOS']);
        Department::create(['title' => 'RENTAS']);
        Department::create(['title' => 'VENTAS MAQ AGRICOLA']);
        Department::create(['title' => 'VENTAS MAQ INDUSTRIAL']);
    }
}
