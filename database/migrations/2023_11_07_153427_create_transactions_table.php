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
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->decimal('amount', 20, 8);
            $table->decimal('fee', 20, 8)->default(0);
            $table->decimal('amount_in_base_currency', 20, 8);
            $table->string('transactionable_type')->nullable();
            $table->unsignedBigInteger('transactionable_id')->nullable();
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
            $table->index(['type', 'status']);
            $table->index(['transactionable_type', 'transactionable_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
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
