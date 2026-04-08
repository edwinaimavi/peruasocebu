<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorías base del sistema
        $categories = [
            [
                'name' => 'Sistemas',
                'slug' => 'sistemas',
                'description' => 'Sistemas de gestión y soluciones empresariales',
                'status' => 1,
            ],
            [
                'name' => 'Servicios',
                'slug' => 'servicios',
                'description' => 'Servicios tecnológicos y consultoría',
                'status' => 1,
            ],
            [
                'name' => 'Soporte',
                'slug' => 'soporte',
                'description' => 'Planes y servicios de soporte técnico',
                'status' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Datos fake adicionales (opcional)
        Category::factory()->count(5)->create();
    }
}
