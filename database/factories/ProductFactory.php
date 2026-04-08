<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'category_id'       => Category::factory(),
            'name'              => ucfirst($name),
            'slug'              => Str::slug($name),
            'short_description' => $this->faker->sentence(10),
            'description'       => $this->faker->paragraphs(3, true),
            'price'             => $this->faker->randomFloat(2, 50, 5000),
            'type'              => $this->faker->randomElement(['sistema', 'servicio']),
            'image'             => null,
            'status'            => $this->faker->randomElement(['published', 'draft']),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
