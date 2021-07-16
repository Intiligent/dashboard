<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Username',
                'email' => 'username@test.com',
                'password' => bcrypt('secret'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
