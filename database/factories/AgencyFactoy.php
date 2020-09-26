<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Components\Common\Models\Agency::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->name,
    ];
});
