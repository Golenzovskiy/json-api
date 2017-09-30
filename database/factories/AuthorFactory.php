<?php

use Faker\Generator as Faker;
use App\Author as Model;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'login' => $faker->unique()->userName
    ];
});