<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Models\Payment;
use App\Models\Withdrawal;
use Illuminate\Support\ServiceProvider;

class CommonDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->share('newMerchants', Merchant::where('approved', false)
            ->where('rejected', false)
            ->count()) ?? 0;

        view()->share('newPayment', Payment::where('status', Payment::STATUS_COMPLETED_STRING)
            ->whereNotNull('receipt')
            ->count()) ?? 0;

        view()->share('newWithdrawal', Withdrawal::where('approved', false)
            ->where('canceled', false)
            ->count()) ?? 0;
    }
}
