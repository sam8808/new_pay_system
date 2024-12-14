<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем транзакции для платежей
        Payment::where('status', Payment::STATUS_COMPLETED_STRING)->get()->each(function ($payment) {
            $wallet = Wallet::firstOrCreate([
                'user_id' => $payment->merchant->user_id,
                'currency_id' => $payment->currency_id
            ]);

            Transaction::factory()->create([
                'user_id' => $payment->merchant->user_id,
                'wallet_id' => $wallet->id,
                'currency_id' => $payment->currency_id,
                'transactionable_type' => Payment::class,
                'transactionable_id' => $payment->id,
                'amount' => $payment->amount,
                'fee' => $payment->processing_fee,
                'amount_in_base_currency' => $payment->amount_in_base_currency,
                'type' => 'deposit',
                'status' => 'completed',
                'metadata' => [
                    'merchant_id' => $payment->merchant_id,
                    'order_id' => $payment->order_id,
                    'payment_system' => $payment->payment_system_id,
                    'processed_at' => $payment->processed_at,
                ]
            ]);
        });

        // Создаем транзакции для выводов
        Withdrawal::where('status', 'completed')->get()->each(function ($withdrawal) {
            Transaction::factory()->create([
                'user_id' => $withdrawal->user_id,
                'wallet_id' => $withdrawal->wallet_id,
                'currency_id' => $withdrawal->currency_id,
                'transactionable_type' => Withdrawal::class,
                'transactionable_id' => $withdrawal->id,
                'amount' => $withdrawal->amount,
                'fee' => $withdrawal->processing_fee,
                'amount_in_base_currency' => $withdrawal->amount_in_base_currency,
                'type' => 'withdrawal',
                'status' => 'completed',
                'metadata' => [
                    'payment_system' => $withdrawal->payment_system_id,
                    'recipient_data' => $withdrawal->recipient_data,
                    'external_id' => $withdrawal->external_id,
                    'processed_at' => $withdrawal->processed_at,
                ]
            ]);
        });
    }
}
