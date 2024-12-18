<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class FraudMonitoringService
{
    protected $rules;

    public function __construct()
    {
        $this->rules = [
            [$this, 'checkTransactionLimit'],
            [$this, 'checkRepeatedTransaction'],
        ];
    }

    public function analyzeTransaction($transaction): bool
    {
        foreach ($this->rules as $rule) {
            $result = call_user_func($rule, $transaction);

            if ($result['is_fraud']) {
                $this->notifyAdmin($result['message']);
                return true;
            }
        }

        return false;
    }

    protected function checkTransactionLimit($transaction): array
    {
        $maxTransactionAmount = 10000;

        if ($transaction->amount > $maxTransactionAmount) {
            return [
                'is_fraud' => true,
                'message' => "Transaction exceeds the limit of $maxTransactionAmount. User ID: {$transaction->user_id}",
            ];
        }

        return ['is_fraud' => false];
    }

    protected function checkRepeatedTransaction($transaction): array
    {
        $recentTransaction = Transaction::where('user_id', $transaction->user_id)
            ->where('amount', $transaction->amount)
            ->where('created_at', '>=', now()->subMinutes(1))
            ->exists();

        if ($recentTransaction) {
            return [
                'is_fraud' => true,
                'message' => "Repeated transaction detected for User ID: {$transaction->user_id}",
            ];
        }

        return ['is_fraud' => false];
    }

    protected function notifyAdmin(string $message): void
    {
        AdminNotification::create([
            'type' => 'fraud',
            'message' => $message,
        ]);

        Log::warning("Fraud detected: {$message}");
    }
}
