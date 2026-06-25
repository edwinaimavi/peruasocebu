<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cattle_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cattle_id')->constrained('cattle')->cascadeOnDelete();
            $table->foreignId('seller_owner_id')->nullable()->constrained('owners')->nullOnDelete();
            $table->foreignId('buyer_owner_id')->nullable()->constrained('owners')->nullOnDelete();
            $table->date('sale_date')->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('currency')->nullable()->default('PEN');
            $table->string('payment_method')->nullable();
            $table->string('contract_file_path')->nullable();
            $table->longText('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cattle_sales');
    }
};
