<?php

use Faker\Generator as Faker;

$factory->define(App\Teacher::class, function (Faker $faker) {
    return [
        //
        'user_id' => App\User::create([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'role_id' => 2,
            'password' => bcrypt(123456), // secret
            'remember_token' => str_random(10),
        ])->id,
        'subject' => $faker->randomDigit,
        'contact_no' => 9887554425,
        'address' => $faker->address
    ];
});
