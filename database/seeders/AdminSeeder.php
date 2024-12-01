<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Создать администратора с использованием фабрики
        Admin::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('admin123456789'),
            'role' => 'admin',
        ]);
    }
}
