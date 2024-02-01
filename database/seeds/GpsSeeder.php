<?php

use Illuminate\Database\Seeder;

class GpsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gps_group = factory(App\Components\Gps\Models\Gps::class, 5)->create();
    }
}
