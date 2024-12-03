<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Payment, Withdrawal, Transaction, Wallet};

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        Payment::where('status', 'completed')->get()->each(function ($payment) {
            Transaction::factory()->create([
                'transactionable_type' => Payment::class,
                'transactionable_id' => $payment->id,
                'amount' => $payment->amount,
                'fee' => $payment->processing_fee,
                'type' => 'deposit',
                'status' => 'completed'
            ]);
        });

        Withdrawal::where('status', 'completed')->get()->each(function ($withdrawal) {
            Transaction::factory()->create([
                'transactionable_type' => Withdrawal::class,
                'transactionable_id' => $withdrawal->id,
                'amount' => $withdrawal->amount,
                'fee' => $withdrawal->processing_fee,
                'type' => 'withdrawal',
                'status' => 'completed'
            ]);
        });
    }
}
