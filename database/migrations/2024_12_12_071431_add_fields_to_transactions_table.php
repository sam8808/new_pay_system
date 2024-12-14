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
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('currency_id')->nullable()->after('wallet_id');

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('set null');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');

            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);

            // Добавляем soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
            $table->dropForeign(['currency_id']);

            $table->dropColumn('wallet_id');
            $table->dropColumn('currency_id');

            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);

            $table->dropSoftDeletes();
        });
    }
};
