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
        Schema::create('gateway_payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->string('order_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10);
            $table->integer('status')->default(0); // created, processing, completed, failed, refunded
            $table->unsignedBigInteger('payment_system_id')->nullable();
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateway_payment');
    }
};
