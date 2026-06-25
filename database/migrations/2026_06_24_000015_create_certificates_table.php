<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('cattle_id')->constrained('cattle')->cascadeOnDelete();
            $table->foreignId('ranch_id')->nullable()->constrained('ranches')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('owners')->nullOnDelete();
            $table->foreignId('veterinarian_id')->nullable()->constrained('veterinarians')->nullOnDelete();
            $table->date('issue_date')->nullable();
            $table->decimal('purity_percentage', 5, 2)->nullable();
            $table->string('certificate_type')->nullable();
            $table->string('verification_code')->unique();
            $table->string('qr_code_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->longText('observations')->nullable();
            $table->string('status')->default('issued');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
