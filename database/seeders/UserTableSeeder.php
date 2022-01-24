<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => 'admin',
            'user_name' => 'admin',
            'avatar' => null,
            'email' => 'admin@mail.com',
            'role' => 'admin',
            'register_at' => now(),
            'password' => bcrypt('password'), // password
            'is_verified' => 1,
            'verification_code' => '123456',
        ]);
    }
}
