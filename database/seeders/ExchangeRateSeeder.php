<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        Currency::all()->each(
            fn($fromCurrency) =>
            Currency::where('id', '!=', $fromCurrency->id)->each(
                fn($toCurrency) =>
                ExchangeRate::factory()->create([
                    'from_currency_id' => $fromCurrency->id,
                    'to_currency_id' => $toCurrency->id,
                ])
            )
        );
    }
}
