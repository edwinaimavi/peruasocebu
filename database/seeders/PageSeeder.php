<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'nosotros'],
            [
                'title'   => 'Nosotros',
                'content' => '<p>Somos una empresa comprometida con nuestros valores.</p>',
                'status'  => 'published',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'contacto'],
            [
                'title'   => 'Contacto',
                'content' => '<p>Puedes contactarnos vía email o formulario.</p>',
                'status'  => 'published',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'terminos'],
            [
                'title'   => 'Términos y Condiciones',
                'content' => '<p>Estos son los términos y condiciones del sitio.</p>',
                'status'  => 'published',
            ]
        );
    }
}
