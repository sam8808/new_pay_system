<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\WebhookNotification;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebhookNotification>
 */
class WebhookNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = WebhookNotification::class;

    public function definition(): array
    {
        return [
            'payment_id' => $this->faker->randomNumber(5, true), // Random 5-digit payment ID
            'event_type' => $this->faker->randomElement(['payment_created', 'payment_processed', 'payment_failed']), // Random event type
            'payload' => json_encode($this->faker->randomElements([
                'key' => $this->faker->word,
                'value' => $this->faker->sentence,
            ], 2)),
            'status' => $this->faker->randomElement(['pending', 'processed', 'failed']), // Random status
            'attempts' => $this->faker->numberBetween(0, 5), // Random number of attempts
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random datetime in the last year
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Random datetime in the last year
            'processed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'), // Optional random datetime
        ];
    }

}
