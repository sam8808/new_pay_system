<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MerchantCoupons;

class MerchantCouponsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 10 MerchantCoupons records
        MerchantCoupons::factory(10)->create();
    }
}
