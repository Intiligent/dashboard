<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Article;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->name();
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'text' => fake()->randomHtml(),
            'active' => fake()->boolean(),
            'lang_id' => rand(1, 3),
            'created_at' => fake()->dateTimeInInterval('-1 week', '+3 days'),
        ];
    }
}
