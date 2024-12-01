<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\PaymentSystem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class PaymentSystemSeeder extends Seeder
{
    public function run(): void
    {
        Currency::all()->each(function ($currency) {
            PaymentSystem::factory()->create([
                'currency_id' => $currency->id,
                'code' => 'stripe_' . strtolower($currency->code)
            ]);

            PaymentSystem::factory()->create([
                'currency_id' => $currency->id,
                'code' => 'paypal_' . strtolower($currency->code)
            ]);

            if ($currency->type === 'crypto') {
                PaymentSystem::factory()->create([
                    'currency_id' => $currency->id,
                    'code' => 'coinbase_' . strtolower($currency->code)
                ]);
            }
        });
    }
}
