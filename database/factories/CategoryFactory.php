<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use NamespacedApp\Category;
use Faker\Generator as Faker;

$factory->define(App\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
