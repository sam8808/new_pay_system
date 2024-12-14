<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Wallet\CreateWalletsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateUserWallets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        try {
            $service = CreateWalletsService::init($this->user);
            $service->create();

            if ($service->fail()) {
                Log::error('Failed to create user wallets in job', [
                    'user_id' => $this->user->id,
                    'errors' => $service->getErrors()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in CreateUserWallets job', [
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}