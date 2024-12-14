<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MerchantCoupons>
 */
class MerchantCouponsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'merchant_id' => $this->faker->numberBetween(1, 4000),
            'payment_id' =>  $this->faker->numberBetween(1, 4000),
            'code' => $this->faker->unique()->regexify('[A-Z0-9]{16}'),
            'amount' => $this->faker->randomFloat(8, 1, 1000),
            'currency_id' =>  $this->faker->numberBetween(1, 4000),
            'status' => $this->faker->randomElement(['pending', 'verified', 'used', 'expired', 'canceled']),
            'operator_id' => $this->faker->boolean(50) ? \App\Models\User::factory() : null, // Assume operators are users; adjust as needed
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'verified_at' => $this->faker->boolean(50) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'used_at' => $this->faker->boolean(25) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
