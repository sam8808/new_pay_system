<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;



class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'account' => 'F' . rand(12345678, 87654321),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // password
            'telegram' => '@' . $this->faker->userName(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => true,
            'is_verified' => true,
            'last_login_at' => now(),
            'remember_token' => Str::random(10),
            'settings' => [
                'notifications' => true,
                'two_factor' => false,
            ],
        ];
    }
}
