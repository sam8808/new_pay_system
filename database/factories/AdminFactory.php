<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => 'admin',
            'password' => Hash::make('admin123456789'),
            'role' => 'admin',
        ];
    }
}
