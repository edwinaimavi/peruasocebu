<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->cascadeOnDelete();
            $table->foreignId('veterinarian_id')->nullable()->constrained('veterinarians')->nullOnDelete();
            $table->string('vaccine_name');
            $table->string('dose')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('application_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->longText('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
