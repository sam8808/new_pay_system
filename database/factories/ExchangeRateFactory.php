<?php

namespace Database\Factories;

use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;



class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    public function definition(): array
    {
        return [
            'rate' => $this->faker->randomFloat(8, 0.0001, 100000),
            'fee_percent' => $this->faker->randomFloat(2, 0, 5),
        ];
    }
}
