<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use Faker\Generator as Faker;

$factory->define(App\Components\Gps\Models\GpsGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'agency' => $faker->name ,
        'departament' => $faker->name,
        'description' => $faker->name,
    ];
});
