<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Merchant;
use App\Models\PaymentSystem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;



class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Merchant::all()->each(fn ($merchant) => 
            PaymentSystem::all()->each(fn ($paymentSystem) => 
                Payment::factory(rand(3, 7))->create([
                    'merchant_id' => $merchant->id,
                    'payment_system_id' => $paymentSystem->id,
                    'currency_id' => $paymentSystem->currency_id,
                ])
            )
        );
    }
}
