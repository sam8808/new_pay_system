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
        Schema::create('merchant_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('payment_id');
            $table->string('code', 16)->unique();
            $table->decimal('amount', 20, 8);
            $table->unsignedBigInteger('currency_id');
            $table->enum('status', ['pending', 'verified', 'used', 'expired', 'canceled']);
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_coupons');
    }
};
