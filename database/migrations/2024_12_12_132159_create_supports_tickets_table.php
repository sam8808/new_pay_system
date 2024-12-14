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
        Schema::create('supports_tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedBigInteger('user_id');
            $table->string('subject');
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['open', 'closed', 'in_progress', 'waiting_for_customer', 'resolved']);
            $table->enum('category', ['technical', 'financial', 'account', 'wallet', 'other']);
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supports_tickets');
    }
};
