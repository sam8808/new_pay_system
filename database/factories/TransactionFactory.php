<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(8, 10, 1000);
        $fee = $amount * 0.02; // 2% комиссия

        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
            'currency_id' => Currency::factory(),
            'amount' => $amount,
            'fee' => $fee,
            'amount_in_base_currency' => $amount * 1.1,
            'type' => $this->faker->randomElement([
                'deposit',
                'withdrawal',
                'transfer',
                'exchange',
                'fee',
                'referral',
                'refund'
            ]),
            'status' => $this->faker->randomElement([
                'pending',
                'completed',
                'failed',
                'canceled'
            ]),
            'metadata' => [
                'ip' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
                'browser' => $this->faker->chrome(),
                'platform' => $this->faker->randomElement(['web', 'mobile', 'api']),
                'created_from' => $this->faker->ipv4(),
                'processed_at' => now()->toDateTimeString(),
            ],
        ];
    }

    // Состояния для разных типов транзакций
    public function deposit()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'deposit',
            ];
        });
    }

    public function withdrawal()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'withdrawal',
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }
}
