<?php

use Illuminate\Database\Seeder;
use App\Imports\MarketingImport;

class MarketingImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new MarketingImport)->import('Historico_Venta_2012_2020.xlsx', 'public', \Maatwebsite\Excel\Excel::XLSX);
    }
}
