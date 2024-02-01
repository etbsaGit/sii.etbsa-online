<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Components\Common\Models\Department::class, function (Faker $faker) {
    return [
        'title' => ucwords($faker->words(3, true)),
        'description' => $faker->text(300),
        'key' => str_replace(' ', '', $faker->words(3, true)),
    ];
});
