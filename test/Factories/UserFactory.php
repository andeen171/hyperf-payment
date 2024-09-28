<?php

declare(strict_types=1);

use App\Enum\UserTypeEnum;
use App\Model\User;
use Faker\Generator as Faker;
use Hyperf\Database\Model\Factory;

/* @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'type' => $faker->randomElement(UserTypeEnum::values()),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'document' => $faker->numerify('###########'),
    ];
});
