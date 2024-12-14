<?php

namespace Database\Seeders;

use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Models\PaymentSystem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;



class WithdrawalSeeder extends Seeder
{
    public function run(): void
    {
        Wallet::all()->each(fn ($wallet) => 
            PaymentSystem::where('currency_id', $wallet->currency_id)->each(fn ($paymentSystem) => 
                Withdrawal::factory(rand(1, 3))->create([
                    'user_id' => $wallet->user_id,
                    'wallet_id' => $wallet->id,
                    'payment_system_id' => $paymentSystem->id,
                    'currency_id' => $wallet->currency_id,
                ])
            )
        );
    }
}
