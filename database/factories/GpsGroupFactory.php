<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Components\Gps\Models\GpsGroup::class, function (Faker $faker) {
    $agencies = [
        'CELAYA',
        'SALAMANCA',
        'IRAPUATO',
    ];
    $departments= [
        "ADMINISTRACION",
        "CONTABILIDAD",
        "DIRECCION",
        "RIEGO",
        "SERVICIO",
    ];
    return [
        'name' => $faker->unique()->name,
        'agency' => $agencies[$faker->numberBetween(0, 2)],
        'department' => $departments[$faker->numberBetween(0, 4)],
        'description' => ucwords($faker->words(4, true)),
    ];
});
