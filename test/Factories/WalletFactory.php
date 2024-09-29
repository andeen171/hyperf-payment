<?php

declare(strict_types=1);

use App\Model\User;
use App\Model\Wallet;
use Faker\Generator as Faker;
use Hyperf\Database\Model\Factory;

/* @var Factory $factory */
$factory->define(Wallet::class, function (Faker $faker) {
    return [
        'balance' => $faker->numberBetween(1000, 100000),
        'user_id' => \factory(User::class)->create()->id,
    ];
});
