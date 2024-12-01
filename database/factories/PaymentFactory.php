<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;



class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);
        $fee = $amount * 0.02; // 2% fee
        
        return [
            'uuid' => Str::uuid(),
            'external_id' => $this->faker->uuid(),
            'order_id' => 'ORD-' . $this->faker->unique()->randomNumber(8),
            'amount' => $amount,
            'processing_fee' => $fee,
            'amount_in_base_currency' => $amount * 1.1, // Example conversion rate
            'payer_email' => $this->faker->email(),
            'payer_phone' => $this->faker->phoneNumber(),
            'metadata' => [
                'ip' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
            ],
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
            'expires_at' => now()->addHours(24),
            'processed_at' => now(),
        ];
    }
}
