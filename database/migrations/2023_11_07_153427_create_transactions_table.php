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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('currency_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('withdrawal_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('amount_in_base_currency', 20, 8);
            $table->enum('type', [
                'deposit',
                'withdrawal',
                'transfer',
                'exchange',
                'fee',
                'referral',
                'refund'
            ]);
            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'canceled'
            ])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('uuid');
            $table->index(['user_id', 'type', 'status']);
            $table->index('wallet_id');
            $table->index('payment_id');
            $table->index('withdrawal_id');
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
