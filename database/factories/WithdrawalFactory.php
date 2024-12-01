<?php

namespace Database\Factories;

use App\Models\Withdrawal;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;



class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);
        $fee = $amount * 0.02; // 2% fee

        return [
            'uuid' => Str::uuid(),
            'external_id' => $this->faker->uuid(),
            'amount' => $amount,
            'processing_fee' => $fee,
            'amount_in_base_currency' => $amount * 1.1,
            'recipient_data' => json_encode([
                'account' => $this->faker->iban(),
                'name' => $this->faker->name(),
            ]),
            'metadata' => [
                'ip' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
            ],
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
            'processed_at' => now(),
        ];
    }
}
