<?php

use App\Components\FlujoEfectivo\Models\TipoPoliza;
use Illuminate\Database\Seeder;

class TipoPolizaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoPoliza::create([
            'key' => 'ingreso',
            'title' => 'Ingreso'
        ]);
        TipoPoliza::create([
            'key' => 'egreso',
            'title' => 'Egreso'
        ]);
        TipoPoliza::create([
            'key' => 'tranferencia',
            'title' => 'Tranferencia (Diario)'
        ]);
        TipoPoliza::create([
            'key' => 'unidentified',
            'title' => 'No Identificado'
        ]);
    }
}
