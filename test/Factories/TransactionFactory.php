<?php

declare(strict_types=1);

use App\Model\Transaction;
use App\Model\User;
use Faker\Generator as Faker;
use Hyperf\Database\Model\Factory;

/* @var Factory $factory */
$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'amount' => $faker->numberBetween(1000, 100000),
        'payer_user_id' => \factory(User::class)->create()->id,
        'payee_user_id' => \factory(User::class)->create()->id,
    ];
});
