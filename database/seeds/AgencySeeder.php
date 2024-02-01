<?php

use Illuminate\Database\Seeder;
use App\Components\Common\Models\Agency;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Agency::createMany([
            [
                'code' => '01',
                'title' => 'CELAYA AGRCOLA'
            ],
            [
                'code' => '51',
                'title' => 'CELAYA INDUSTRIAL'
            ],
        ]);
    }
}
