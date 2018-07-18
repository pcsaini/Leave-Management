<?php

use Faker\Generator as Faker;

$factory->define(App\Student::class, function (Faker $faker) {
    return [
        //
        'user_id' => App\User::create([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'role_id' => 3,
            'password' => bcrypt(123456), // secret
            'remember_token' => str_random(10),
        ])->id,
        'class' => $faker->randomDigit,
        'father_name' => $faker->name,
        'contact_no' => 9887665545,
        'address' => $faker->address
    ];
});
