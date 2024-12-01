<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(fn ($user) => 
            Merchant::factory(rand(1, 3))->create([
                'user_id' => $user->id
            ])
        );
    }
}

