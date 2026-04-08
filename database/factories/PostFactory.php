<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'title'   => $title,
            'slug'    => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),

            'content' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',

            'image'   => $this->faker->imageUrl(1200, 630, 'business', true),

            'status'  => $this->faker->randomElement(['draft', 'published']),

            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),

            'category_id' =>Category::inRandomOrder()->first()?->id ?? Category::factory(),
        ];
    }
}
