<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(fn ($user) => 
            Currency::all()->each(fn ($currency) => 
                Wallet::factory()->create([
                    'user_id' => $user->id,
                    'currency_id' => $currency->id
                ])
            )
        );
    }
}
