<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;



class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);
        $fee = $amount * 0.02; // 2% fee

        return [
            'uuid' => Str::uuid(),
            'amount' => $amount,
            'fee' => $fee,
            'amount_in_base_currency' => $amount * 1.1,
            'type' => $this->faker->randomElement(['deposit', 'withdrawal', 'transfer', 'exchange']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'metadata' => [
                'ip' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
            ],
        ];
    }
}
