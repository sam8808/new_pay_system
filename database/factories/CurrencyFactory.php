<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;



class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        static $index = 0;

        $currencies = [
            [
                'type' => 'fiat',
                'title' => 'Russain Ruble',
                'code' => 'RUB',
                'symbol' => '₽',
                'exchange_rate' => 0.9,
                'min_amount' => 1,
                'max_amount' => 100000,
            ],
            [
                'type' => 'fiat',
                'title' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'exchange_rate' => 1,
                'min_amount' => 1,
                'max_amount' => 100000,
            ],
            [
                'type' => 'fiat',
                'title' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'exchange_rate' => 0.85,
                'min_amount' => 1,
                'max_amount' => 100000,
            ],
            [
                'type' => 'fiat',
                'title' => 'British Pound',
                'code' => 'GBP',
                'symbol' => '£',
                'exchange_rate' => 0.73,
                'min_amount' => 1,
                'max_amount' => 100000,
            ],
            [
                'type' => 'crypto',
                'title' => 'Bitcoin',
                'code' => 'BTC',
                'symbol' => '₿',
                'exchange_rate' => 35000,
                'min_amount' => 0.0001,
                'max_amount' => 10,
            ],
            [
                'type' => 'crypto',
                'title' => 'Ethereum',
                'code' => 'ETH',
                'symbol' => 'Ξ',
                'exchange_rate' => 2000,
                'min_amount' => 0.0001,
                'max_amount' => 10,
            ],
            [
                'type' => 'crypto',
                'title' => 'Tether',
                'code' => 'USDT',
                'symbol' => '₮',
                'exchange_rate' => 1,
                'min_amount' => 0.0001,
                'max_amount' => 10,
            ],
        ];

        $currency = $currencies[$index % count($currencies)];
        $index++;

        return array_merge($currency, [
            'icon' => "currencies/{$currency['code']}.svg",
            'processing_fee' => $this->faker->randomFloat(2, 0, 5),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ]);
    }
}
