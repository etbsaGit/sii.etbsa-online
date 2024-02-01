<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\AgencyBankAccount;
use Faker\Generator as Faker;

$factory->define(AgencyBankAccount::class, function (Faker $faker) {
    return [
        'agency_id' => 1,
        'bank_name' => $faker->name(),
        'account_number' => $faker->bankAccountNumber(),
        'balance' => 100000,
    ];
});
