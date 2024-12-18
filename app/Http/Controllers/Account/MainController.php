<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Withdrawal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Eager load relationships for wallets
        $wallets = $user->wallets()->with('currency')->get();

        // Get merchant IDs once to avoid multiple queries
        $merchantIds = $user->merchants()->pluck('id');

        // Get base statistics
        $stats = $this->getBaseStats($user, $merchantIds);

        // Get recent transactions with related data
        $recentTransactions = $this->getRecentTransactions($user);

        // Get monthly statistics
        $monthlyStats = $this->getMonthlyStats($user, $merchantIds);

        return Inertia::render('Account/Index', [
            'wallets' => $wallets,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function getBaseStats($user, $merchantIds): array
    {
        $today = today();

        return [
            'merchantsCount' => $merchantIds->count(),

            'todayIncome' => Payment::whereIn('merchant_id', $merchantIds)
                ->where('status', Payment::STATUS_COMPLETED_STRING)
                ->whereDate('created_at', $today)
                ->sum('amount_in_base_currency'),

            'todayWithdrawals' => Withdrawal::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('created_at', $today)
                ->sum('amount_in_base_currency'),
        ];
    }

    private function getRecentTransactions($user): array
    {
        return Transaction::where('user_id', $user->id)
            ->with(['transactionable', 'currency', 'wallet'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->uuid,
                    'amount' => $transaction->amount,
                    'fee' => $transaction->fee,
                    'amount_in_base_currency' => $transaction->amount_in_base_currency,
                    'currency' => [
                        'code' => $transaction->currency->code,
                        'symbol' => $transaction->currency->symbol,
                    ],
                    'type' => $transaction->type,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'related_info' => $this->getTransactionRelatedInfo($transaction)
                ];
            })
            ->toArray();
    }

    private function getMonthlyStats($user, $merchantIds): array
    {
        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        $monthlyTransactions = Transaction::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear);

        $monthlyCompletedPayments = Payment::whereIn('merchant_id', $merchantIds)
            ->where('status', Payment::STATUS_COMPLETED_STRING)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear);

        return [
            'successRate' => $this->calculateSuccessRate($merchantIds),
            'averagePayment' => $monthlyCompletedPayments->avg('amount_in_base_currency') ?? 0,
            'merchantsStats' => [
                'active' => $user->merchants()
                    ->where('is_active', true)
                    ->where('is_succes_moderation', true)
                    ->count(),
                'total' => $merchantIds->count()
            ],
            'totalTransactions' => $monthlyTransactions->count(),
            'monthlyVolume' => $monthlyTransactions
                ->where('status', 'completed')
                ->sum('amount_in_base_currency')
        ];
    }

    private function getTransactionRelatedInfo(Transaction $transaction): array
    {
        $info = [];

        if ($transaction->transactionable) {
            switch (get_class($transaction->transactionable)) {
                case Payment::class:
                    $info = [
                        'order_id' => $transaction->transactionable->order_id,
                        'payer_email' => $transaction->transactionable->payer_email,
                    ];
                    break;

                case Withdrawal::class:
                    $info = [
                        'recipient_data' => $transaction->transactionable->recipient_data,
                        'external_id' => $transaction->transactionable->external_id,
                    ];
                    break;
            }
        }

        return $info;
    }

    private function calculateSuccessRate($merchantIds)
    {
        $now = now();
        $totalPayments = Payment::whereIn('merchant_id', $merchantIds)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        if ($totalPayments === 0) {
            return 0;
        }

        $successfulPayments = Payment::whereIn('merchant_id', $merchantIds)
            ->where('status', Payment::STATUS_COMPLETED_STRING)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        return ($successfulPayments / $totalPayments) * 100;
    }

    public function partners()
    {


        return Inertia::render('Account/Partners', [
            'referralStats' => auth()->user()->referralStats(),
            'referrals' => auth()->user()->referrals()
        ]);
    }
}
