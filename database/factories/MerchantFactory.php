<?php

namespace Database\Factories;

use App\Models\Merchant;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;



class MerchantFactory extends Factory
{
    protected $model = Merchant::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->company(),
            'domain' => $this->faker->domainName(),
            'description' => $this->faker->paragraph(),
            'merchant_id' => $this->faker->unique()->numberBetween(100000, 999999),
            'api_key' => Str::random(32),
            'secret_key' => Str::random(32),
            'webhook_url' => $this->faker->url(),
            'type' => $this->faker->randomElement(['api', 'coupon']),
            'processing_fee' => $this->faker->randomFloat(2, 0, 5),
            'allowed_ips' => [$this->faker->ipv4()],
            'is_active' => true,
            'is_succes_moderation' => true,
        ];
    }
}
