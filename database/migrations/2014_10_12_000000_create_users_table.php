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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('account', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->string('telegram')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->json('settings')->nullable();
            $table->enum('two_factor_type', [
                'email',
                'sms'
            ])->default('email');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('account');
            $table->index(['is_active', 'is_verified']);
            $table->index('referrer_id');

            $table->foreign('referrer_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
