<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // 🔹 categoría (OBLIGATORIA)
            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // 🔹 datos principales
            $table->string('name');
            $table->string('slug')->unique();

            $table->string('short_description', 255)->nullable();
            $table->longText('description');

            $table->decimal('price', 10, 2)->default(0);

            // 🔹 tipo
            $table->enum('type', ['sistema', 'servicio']);

            // 🔹 imagen principal (thumbnail)
            $table->string('image')->nullable();

            // 🔹 estado
            $table->enum('status', ['published', 'draft', 'removed'])
                  ->default('draft')
                  ->index(); // 🔥 performance

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};