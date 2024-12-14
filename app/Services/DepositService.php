<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentSystem;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private readonly Request $request,
        private readonly CurrencyRateService $currencyRateService
    ) {}

    public function validate(): static
    {
        $this->request->validate([
            'payment_system_id' => ['required', 'exists:payment_systems,id'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payer_email' => ['nullable', 'email'],
            'payer_phone' => ['nullable', 'string'],
        ]);

        return $this;
    }

    public function create(): Deposit
    {
        try {
            return DB::transaction(function () {
                $deposit = Deposit::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $this->request->user()->id,
                    'payment_system_id' => $this->request->post('payment_system_id'),
                    'currency_id' => $this->request->post('currency_id'),
                    'wallet_id' => $this->request->user()
                        ->wallets()
                        ->where('currency_id', $this->request->post('currency_id'))
                        ->value('id'),
                    'amount' => $this->request->post('amount'),
                    'processing_fee' => $this->calculateProcessingFee(),
                    'amount_in_base_currency' => $this->calculateBaseAmount(),
                    'payer_email' => $this->request->post('payer_email'),
                    'payer_phone' => $this->request->post('payer_phone'),
                    'status' => 'pending',
                    'expires_at' => now()->addHours(1),
                ]);

                // Создаем транзакцию
                $this->createTransaction($deposit);

                return $deposit;
            });
        } catch (\Exception $e) {
            \Log::error('Deposit creation error', [
                'user_id' => $this->request->user()->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function calculateProcessingFee(): float
    {
        $paymentSystem = PaymentSystem::find($this->request->post('payment_system_id'));
        return $this->request->post('amount') * ($paymentSystem->processing_fee / 100);
    }

    private function calculateBaseAmount(): float
    {
        $currency = Currency::find($this->request->post('currency_id'));
        $amount = $this->request->post('amount');

        return $this->currencyRateService->convertToBaseCurrency($amount, $currency);
    }

    private function createTransaction(Deposit $deposit): void
    {
        Transaction::create([
            'uuid' => Str::uuid(),
            'user_id' => $deposit->user_id,
            'wallet_id' => $deposit->wallet_id,
            'currency_id' => $deposit->currency_id,
            'transactionable_type' => Deposit::class,
            'transactionable_id' => $deposit->id,
            'amount' => $deposit->amount,
            'fee' => $deposit->processing_fee,
            'amount_in_base_currency' => $deposit->amount_in_base_currency,
            'type' => 'deposit',
            'status' => 'pending'
        ]);
    }

    public function getAvailablePaymentSystems()
    {
        return PaymentSystem::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('type', 'payment')
                    ->orWhere('type', 'both');
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function getUserDeposits()
    {
        return Deposit::query()
            ->where('user_id', $this->request->user()->id)
            ->with(['currency', 'paymentSystem', 'transaction'])
            ->latest()
            ->paginate(15);
    }

    public function getDepositByUuid(string $uuid): Deposit
    {
        return Deposit::where('uuid', $uuid)
            ->with(['currency', 'paymentSystem', 'transaction'])
            ->firstOrFail();
    }
}
