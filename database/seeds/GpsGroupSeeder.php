<?php

use Illuminate\Database\Seeder;

class GpsGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gps_group = factory(App\Components\Gps\Models\GpsGroup::class, 20)->create();
    }
}
