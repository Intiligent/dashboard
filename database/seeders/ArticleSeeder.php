<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $pages = [
            [
                'title' => 'Услуги',
                'slug' => 'service',
                'text' => 'Наши услуги',
            ],
            [
                'title' => 'О нас',
                'slug' => 'about',
                'text' => 'О нас текст',
            ],
            [
                'title' => 'Оплата и доставка',
                'slug' => 'delivery',
                'text' => 'Оплата и доставка инфо',
            ],
            [
                'title' => 'Контакты',
                'slug' => 'contact',
                'text' => 'Наши контакты',
            ],
        ];

        foreach ($pages as $article) {
            Article::firstOrNew(Arr::only($article, ['title', 'slug']), $article)->save();
        }
    }
}
