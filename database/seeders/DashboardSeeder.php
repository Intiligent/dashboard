<?php

namespace Dashboard\Database\Seeders;

use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MenuSeeder::class,
        ]);
    }
}
