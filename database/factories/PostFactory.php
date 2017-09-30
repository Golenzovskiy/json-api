<?php

use Faker\Generator as Faker;
use App\Post as Model;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'description' => $faker->text($maxNbChars = 200),
        'ip_address' => '192.168.1.' . rand(1, 255)
    ];
});
