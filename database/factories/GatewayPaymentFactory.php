<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\GatewayPayment;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GatewayPayment>
 */
class GatewayPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = GatewayPayment::class;

    public function definition(): array
    {
        return [
            'merchant_id' => $this->faker->randomNumber(5, true), // A random 5-digit number
            'order_id' => $this->faker->unique()->uuid, // Unique identifier for the order
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Random amount between 10 and 1000 with 2 decimal places
            'currency' => $this->faker->currencyCode, // Random currency code (e.g., USD, EUR)
            'status' => $this->faker->numberBetween(0, 4), // Random status
            'payment_system_id' => $this->faker->optional()->randomNumber(5, true), // Optional random 5-digit number
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random datetime in the last year
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random datetime in the last year
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'), // Optional random datetime
        ];
    }
}
