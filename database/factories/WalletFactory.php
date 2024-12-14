<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;



class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'balance' => $this->faker->randomFloat(8, 0, 10000),
            'reserved_balance' => $this->faker->randomFloat(8, 0, 100),
            'is_active' => true,
            'is_show' => true,
            'is_default' => false,
        ];
    }
}

