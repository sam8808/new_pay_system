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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('domain')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('merchant_id')->unsigned()->unique();
            $table->string('api_key', 64)->unique();
            $table->string('secret_key', 64);
            $table->string('webhook_url')->nullable();
            $table->enum('type', ['api', 'coupon'])->default('api');
            $table->decimal('processing_fee', 8, 4)->default(0);
            $table->json('allowed_ips')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_succes_moderation')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('merchant_id');
            $table->index('api_key');
            $table->index(['user_id', 'is_active']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
