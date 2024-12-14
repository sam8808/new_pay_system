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
        $wallets = $user->wallets()
            ->with('currency')
            ->get();

        $merchants = $user->merchants();

        // Базовая статистика
        $stats = [
            'merchantsCount' => $merchants->count(),

            // Входящие платежи за сегодня
            'todayIncome' => Payment::whereIn('merchant_id', $merchants->pluck('id'))
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount_in_base_currency'),

            // Выводы за сегодня    
            'todayWithdrawals' => Withdrawal::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount_in_base_currency'),
        ];

        // Последние транзакции
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with([
                'payment:id,merchant_id,amount,currency_id,status',
                'withdrawal:id,amount,currency_id,status',
                'currency:id,code,symbol',
                'wallet:id,currency_id'
            ])
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
                    // Дополнительная информация в зависимости от типа
                    'related_info' => $this->getRelatedTransactionInfo($transaction)
                ];
            });

        $monthlyStats = [
            // Процент успешных платежей
            'successRate' => $this->calculateSuccessRate($merchants->pluck('id')),

            // Средний платёж за месяц
            'averagePayment' => Payment::whereIn('merchant_id', $merchants->pluck('id'))
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->avg('amount_in_base_currency') ?? 0,

            // Статистика по мерчантам
            'merchantsStats' => [
                'active' => $merchants->where('is_active', true)
                    ->where('is_succes_moderation', true)
                    ->count(),
                'total' => $merchants->count()
            ],

            // Общая статистика за месяц
            'totalTransactions' => Transaction::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'monthlyVolume' => Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount_in_base_currency')
        ];

        return Inertia::render('Account/Index', [
            'wallets' => $wallets,
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function calculateSuccessRate($merchantIds)
    {
        $stats = Payment::whereIn('merchant_id', $merchantIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed')
            )
            ->first();

        return $stats->total > 0
            ? round(($stats->completed / $stats->total) * 100)
            : 0;
    }

    private function getRelatedTransactionInfo($transaction)
    {
        switch ($transaction->type) {
            case 'deposit':
                return $transaction->payment
                    ? ['payment_id' => $transaction->payment->id]
                    : null;
            case 'withdrawal':
                return $transaction->withdrawal
                    ? ['withdrawal_id' => $transaction->withdrawal->id]
                    : null;
            default:
                return null;
        }
    }

    public function partners()
    {
        return Inertia::render('Account/Partners');
    }
}
