<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Wallet\TransferService;
use Illuminate\Support\Facades\Redirect;

class TransferController extends Controller
{
    /**
     * Отображает форму создания перевода с доступными кошельками пользователя
     */
    public function index()
    {
        try {
            // Получаем активные кошельки пользователя с данными о валютах
            $wallets = Wallet::query()
                ->with('currency')
                ->where('user_id', Auth::user()->id)
                ->where('is_active', true)
                ->get()
                ->map(fn($wallet) => [
                    'id' => $wallet->id,
                    'balance' => $wallet->balance,
                    'formatted_balance' => number_format($wallet->balance, $wallet->currency->decimals),
                    'currency' => $wallet->currency->code,
                    'symbol' => $wallet->currency->symbol
                ]);

            // Получаем последние переводы для отображения истории
            $recentTransfers = Transaction::query()
                ->with(['wallet.currency', 'user'])
                ->where('user_id', Auth::user()->id)
                ->where('type', 'transfer')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($transaction) => [
                    'uuid' => $transaction->uuid,
                    'amount' => number_format($transaction->amount, $transaction->wallet->currency->decimals),
                    'currency' => $transaction->wallet->currency->code,
                    'recipient' => $transaction->metadata['recipient_account'],
                    'date' => $transaction->created_at->format('d.m.Y H:i')
                ]);

            return Inertia::render('Wallet/Transfer/Index', [
                'wallets' => $wallets,
                'recentTransfers' => $recentTransfers,
                'hasActiveWallets' => $wallets->isNotEmpty()
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading transfer page: ' . $e->getMessage(), [
                'user_id' => Auth::user()->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('dashboard')->withErrors([
                'error' => __('Unable to load transfer page')
            ]);
        }
    }

    /**
     * Выполняет предварительную проверку возможности перевода
     */
    public function check(Request $request)
    {
        try {
            $service = TransferService::init($request->all());

            if (!$service->validate()) {
                return response()->json([
                    'success' => false,
                    'errors' => $service->getErrors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'recipient' => [
                    'account' => $service->getRecipientAccount(),
                    'wallet' => [
                        'currency' => $service->getRecipientWalletCurrency(),
                        'details' => $service->getSourceWalletDetails()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Transfer validation error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::user()->id
            ]);

            return response()->json([
                'success' => false,
                'errors' => [__('Unable to validate transfer')]
            ], 500);
        }
    }

    /**
     * Выполняет перевод средств и обрабатывает результат
     */
    public function store(Request $request)
    {
        try {
            $service = TransferService::init($request->all());

            if (!$service->validate()) {
                return Redirect::back()
                    ->withErrors($service->getErrors())
                    ->withInput();
            }

            $service->store();

            if ($service->fail()) {
                return Redirect::back()
                    ->withErrors($service->getErrors())
                    ->withInput();
            }

            // Получаем отформатированные данные о транзакции
            $transactionDetails = $service->getTransactionDetails();

            return Redirect::route('wallet.transfers.success', [
                'transaction' => $transactionDetails['uuid']
            ])->with('success', __('Transfer completed successfully'));
        } catch (\Exception $e) {
            Log::error('Transfer execution error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'user_id' => Auth::user()->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::back()
                ->withErrors(['error' => __('Unable to process transfer')])
                ->withInput();
        }
    }

    /**
     * Отображает страницу успешного завершения перевода
     */
    public function success(string $uuid)
    {
        try {
            $transaction = Transaction::with(['wallet.currency', 'user'])
                ->where('uuid', $uuid)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            $service = TransferService::init([
                'from_wallet_id' => $transaction->wallet_id,
                'to_account' => $transaction->metadata['recipient_account'],
                'amount' => $transaction->amount
            ]);

            return Inertia::render('Wallet/Transfer/Success', [
                'transaction' => $service->getTransactionDetails()
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading transfer success page: ' . $e->getMessage(), [
                'transaction_uuid' => $uuid,
                'user_id' => Auth::user()->id
            ]);

            return Redirect::route('wallet.transfers.index')
                ->withErrors(['error' => __('Transaction not found')]);
        }
    }
}
