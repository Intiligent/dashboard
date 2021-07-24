<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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
                'password' => 'secret',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrNew(Arr::only($user, ['email']), $user)->save();
        }
    }
}
