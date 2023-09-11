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
                'name' => 'Vitaliy Artyukh',
                'email' => 'vitaliy.artyukh@gmail.com',
                'avatar' => 'https://lh3.googleusercontent.com/a/AAcHTtcsWsVUkoFjJ62PCN9WNeyc4yWl32qo2uWSfqQ9tCZncHo=s83-c-mo',
                'password' => 'secret',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrNew(Arr::only($user, ['email']), $user)->save();
        }
    }
}
