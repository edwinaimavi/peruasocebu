<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurar al menos un usuario
        if (User::count() === 0) {
            User::factory()->create();
        }

        // Crear posts
        Post::factory()
            ->count(15)
            ->create();
    }
}
