<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cattle_genealogy_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->cascadeOnDelete();
            $table->foreignId('relative_cattle_id')->nullable()->constrained('cattle')->nullOnDelete();
            $table->string('relation_type');
            $table->unsignedTinyInteger('generation_level')->default(1);
            $table->string('relative_code')->nullable();
            $table->string('relative_name')->nullable();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->decimal('purity_percentage', 5, 2)->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cattle_genealogy_links');
    }
};
