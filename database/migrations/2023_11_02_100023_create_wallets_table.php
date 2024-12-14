<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 20, 8)->default(0);
            $table->decimal('reserved_balance', 20, 8)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_show')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'currency_id']);
            $table->index(['user_id', 'is_active']);
            $table->index('currency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
