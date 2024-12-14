<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateWalletsService
{
    private User $user;
    private array $errors = [];
    private bool $fail = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function init(User $user): self
    {
        return new self($user);
    }

    public function create(): void
    {
        try {
            DB::transaction(function () {
                Currency::where('is_active', true)
                    ->get()
                    ->each(function (Currency $currency, int $index) {
                        $this->createWallet($currency, $index);
                    });
            });
        } catch (\Exception $e) {
            $this->handleError($e);
            throw $e;
        }
    }

    private function createWallet(Currency $currency, int $index): void
    {
        try {
            Wallet::create([
                'user_id' => $this->user->id,
                'currency_id' => $currency->id,
                'balance' => 0,
                'reserved_balance' => 0,
                'is_active' => true,
                'is_show' => true,
                'is_default' => false,
            ]);

            Log::info('Wallet created', [
                'user_id' => $this->user->id,
                'currency' => $currency->code,
                'is_default' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create wallet', [
                'user_id' => $this->user->id,
                'currency' => $currency->code,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function fail(): bool
    {
        return $this->fail;
    }

    private function handleError(\Exception $e): void
    {
        $this->fail = true;
        Log::error('Error creating wallets: ' . $e->getMessage(), [
            'user_id' => $this->user->id,
            'trace' => $e->getTraceAsString()
        ]);
        $this->errors[] = __('Failed to create user wallets');
    }
}
