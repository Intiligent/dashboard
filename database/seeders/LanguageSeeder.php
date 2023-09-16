<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'name' => 'English',
                'code' => 'en',
                'icon' => '🇺🇸',
            ],
            [
                'name' => 'Русский',
                'code' => 'ru',
                'icon' => '🇷🇺',
            ],
            [
                'name' => 'Українська',
                'code' => 'uk',
                'icon' => '🇺🇦',
            ],
        ];

        foreach ($languages as $language) {
            Language::firstOrNew(Arr::only($language, ['code']), $language)->save();
        }
    }
}
