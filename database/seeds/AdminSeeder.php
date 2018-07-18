<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('role')->insert([
            'id' => 1,
            'role' => 'admin'
        ]);
        DB::table('role')->insert([
            'id' => 2,
            'role' => 'teacher'
        ]);
        DB::table('role')->insert([
            'id' => 3,
            'role' => 'student'
        ]);

        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('123456'),
        ]);
    }
}
