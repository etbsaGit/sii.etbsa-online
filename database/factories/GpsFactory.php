<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Components\Gps\Models\GpsGroup;
use Carbon\Carbon;

$factory->define(App\Components\Gps\Models\Gps::class, function (Faker $faker) {

    return [
        'name' => $faker->unique()->name,
        'uploaded_by' => 1 ,
        'gps_group_id' => GpsGroup::find($faker->numberBetween(0, 4))->id,
        'sim' => $faker->e164PhoneNumber,
        'imei' => $faker->ean13,
        'cost' => 0,
        'amount' => 0,
        'activation_date' => Carbon::now(),
        'due_date' => Carbon::now()->add(1,'year'),
    ];
});
