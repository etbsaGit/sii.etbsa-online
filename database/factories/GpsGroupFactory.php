<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Components\Gps\Models\GpsGroup::class, function (Faker $faker) {
    $agencies = [
        'CELAYA',
        'SALAMANCA',
        'IRAPUATO',
        'QUERETARO COLORADO',
        'QUERETARO COLORADO',
        'ABASOLO',
    ];
    $departments= [
        "ADMINISTRACION",
        "CONTABILIDAD",
        "DIRECCION",
        "RIEGO",
        "SERVICIO",
        "LUBRICANTES",
        "NUEVAS TECNOLOGIAS",
        "REFACCIONES",
    ];
    return [
        'name' => $faker->unique()->name,
        'agency' => $agencies[$faker->numberBetween(0, 5)],
        'department' => $departments[$faker->numberBetween(0, 7)],
        'description' => ucwords($faker->words(4, true)),
    ];
});
