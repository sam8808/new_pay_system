<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('code', 10)->unique();
            $table->string('symbol', 10)->nullable();
            $table->string('icon')->nullable();
            $table->enum('type', ['fiat', 'crypto'])->default('fiat');
            $table->decimal('exchange_rate', 20, 8)->default(1);
            $table->decimal('min_amount', 20, 8)->default(0);
            $table->decimal('max_amount', 20, 8)->nullable();
            $table->decimal('processing_fee', 8, 4)->default(0); // Percentage
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('code');
            $table->index(['is_active', 'type']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
