<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Payment, Withdrawal, Transaction, Wallet};

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        Payment::where('status', 'completed')->get()->each(function ($payment) {
            $wallet = Wallet::where('user_id', $payment->merchant->user_id)
                ->where('currency_id', $payment->currency_id)
                ->first();

            Transaction::factory()->create([
                'payment_id' => $payment->id,
                'user_id' => $payment->merchant->user_id,
                'wallet_id' => $wallet->id,
                'currency_id' => $payment->currency_id,
                'type' => 'deposit',
                'amount' => $payment->amount,
                'fee' => $payment->processing_fee,
            ]);
        });

        Withdrawal::where('status', 'completed')->get()->each(function ($withdrawal) {
            Transaction::factory()->create([
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'wallet_id' => $withdrawal->wallet_id,
                'currency_id' => $withdrawal->currency_id,
                'type' => 'withdrawal',
                'amount' => $withdrawal->amount,
                'fee' => $withdrawal->processing_fee,
            ]);
        });
    }
}
