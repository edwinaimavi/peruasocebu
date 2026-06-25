<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cattle', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->foreignId('ranch_id')->nullable()->constrained('ranches')->nullOnDelete();
            $table->foreignId('current_owner_id')->nullable()->constrained('owners')->nullOnDelete();
            $table->foreignId('father_id')->nullable()->constrained('cattle')->nullOnDelete();
            $table->foreignId('mother_id')->nullable()->constrained('cattle')->nullOnDelete();
            $table->string('sex')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('color')->nullable();
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->decimal('height_cm', 10, 2)->nullable();
            $table->string('ear_tag')->nullable();
            $table->string('chip_number')->nullable();
            $table->decimal('purity_percentage', 5, 2)->nullable();
            $table->string('status')->default('active');
            $table->string('sale_status')->default('not_available');
            $table->string('main_photo_path')->nullable();
            $table->boolean('is_public')->default(true);
            $table->longText('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cattle');
    }
};
