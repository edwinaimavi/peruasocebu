<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reproduction_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->cascadeOnDelete();
            $table->foreignId('partner_cattle_id')->nullable()->constrained('cattle')->nullOnDelete();
            $table->string('method')->nullable();
            $table->date('reproduction_date')->nullable();
            $table->date('pregnancy_check_date')->nullable();
            $table->string('pregnancy_result')->nullable();
            $table->date('birth_date')->nullable();
            $table->foreignId('offspring_cattle_id')->nullable()->constrained('cattle')->nullOnDelete();
            $table->longText('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reproduction_records');
    }
};
