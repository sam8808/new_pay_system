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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('external_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_system_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('processing_fee', 20, 8)->default(0);
            $table->decimal('amount_in_base_currency', 20, 8);
            $table->string('recipient_data');
            $table->json('metadata')->nullable();
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'canceled'
            ])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('uuid');
            $table->index('external_id');
            $table->index(['user_id', 'status']);
            $table->index('wallet_id');
            $table->index('payment_system_id');
            $table->index('status');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
