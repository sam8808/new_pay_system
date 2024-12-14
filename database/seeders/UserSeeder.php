<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'account' => 'F'.rand(12345678, 87654321),
        ]);

        User::factory(10)->create();
    }
}

