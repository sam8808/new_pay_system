<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\PaymentSystem;
use Illuminate\Database\Eloquent\Factories\Factory;


class PaymentSystemFactory extends Factory
{
    protected $model = PaymentSystem::class;

    public function definition(): array
    {
        static $index = 0;

        $systems = [
            [
                'title' => 'Stripe',
                'code' => 'stripe',
                'provider' => 'stripe',
                'type' => 'payment',
            ],
            [
                'title' => 'PayPal',
                'code' => 'paypal',
                'provider' => 'paypal',
                'type' => 'both',
            ],
            [
                'title' => 'Coinbase',
                'code' => 'coinbase',
                'provider' => 'coinbase',
                'type' => 'both',
            ],
            [
                'title' => 'Wise',
                'code' => 'wise',
                'provider' => 'wise',
                'type' => 'withdrawal',
            ],
        ];

        $system = $systems[$index % count($systems)];
        $index++;

        return [
            'title' => $system['title'],
            'code' => $system['code'],
            'description' => $this->faker->paragraph(),
            'provider' => $system['provider'],
            'provider_settings' => [
                'api_key' => Str::random(32),
                'secret_key' => Str::random(32),
            ],
            'logo' => "payment-systems/{$system['code']}.svg",
            'type' => $system['type'],
            'min_amount' => $this->faker->randomFloat(2, 1, 10),
            'max_amount' => $this->faker->randomFloat(2, 1000, 10000),
            'processing_fee' => $this->faker->randomFloat(2, 0, 5),
            'processing_time' => $this->faker->randomElement([0, 15, 30, 60]),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
