<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AdminSeeder;
use Database\Seeders\WalletSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\MerchantSeeder;
use Database\Seeders\WithdrawalSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\ExchangeRateSeeder;
use Database\Seeders\PaymentSystemSeeder;
use Database\Seeders\GatewayPaymentSeeder;
use Database\Seeders\MerchantCouponsSeeder;
use Database\Seeders\WebhookNotificationSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
<<<<<<< Updated upstream
            AdminSeeder::class,
            UserSeeder::class,
            CurrencySeeder::class,
            WalletSeeder::class,
            MerchantSeeder::class,
            PaymentSystemSeeder::class,
            PaymentSeeder::class,
            WithdrawalSeeder::class,
            TransactionSeeder::class,
            ExchangeRateSeeder::class,
            GatewayPaymentSeeder::class,
            MerchantCouponsSeeder::class,
            WebhookNotificationSeeder::class,
=======
            // AdminSeeder::class,
            // UserSeeder::class,
            // CurrencySeeder::class,
            // WalletSeeder::class,
            // MerchantSeeder::class,
            // PaymentSystemSeeder::class,
            // PaymentSeeder::class,
            // WithdrawalSeeder::class,
            // TransactionSeeder::class,
            // ExchangeRateSeeder::class,
            // GatewayPaymentSeeder::class,
            // WebhookNotificationSeeder::class,
>>>>>>> Stashed changes
        ]);
    }
}
