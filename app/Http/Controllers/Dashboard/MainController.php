<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $merchants = $user->merchants();

        // Статистика
        $stats = [
            'totalBalance' => $merchants->sum('balance'),
            'merchantsCount' => $merchants->count(),

            // Входящие платежи за сегодня
            'todayIncome' => Transaction::whereIn('m_id', $merchants->pluck('id'))
                ->where('type', 'in')
                ->where('confirmed', true)
                ->whereDate('created_at', today())
                ->sum('amount'),

            // Выводы за сегодня
            'todayWithdrawals' => Transaction::whereIn('m_id', $merchants->pluck('id'))
                ->where('type', 'out')
                ->where('confirmed', true)
                ->whereDate('created_at', today())
                ->sum('amount'),
        ];

        // Последние транзакции
        $recentTransactions = Transaction::whereIn('m_id', $merchants->pluck('id'))
            ->with('merchant:id,title')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'm_id' => $transaction->m_id,
                    'payment_id' => $transaction->payment_id,
                    'withdrawal_id' => $transaction->withdrawal_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'type' => $transaction->type,
                    'confirmed' => $transaction->confirmed,
                    'canceled' => $transaction->canceled,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s')
                ];
            });

        $monthlyStats = [
            // Процент успешных платежей
            'successRate' => Transaction::whereIn('m_id', $merchants->pluck('id'))
                ->where('type', 'in')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->when(true, function ($query) {
                    $total = $query->count();
                    $successful = $query->where('confirmed', true)->count();
                    return $total > 0 ? round(($successful / $total) * 100) : 0;
                }),

            // Средний чек за месяц
            'averagePayment' => Transaction::whereIn('m_id', $merchants->pluck('id'))
                ->where('type', 'in')
                ->where('confirmed', true)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->avg('amount') ?? 0,

            // Статистика по магазинам
            'merchantsStats' => [
                'active' => $merchants->where('activated', true)->count(),
                'total' => $merchants->count()
            ],

            // Дополнительная статистика
            'totalTransactions' => Transaction::whereIn('m_id', $merchants->pluck('id'))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'monthlyVolume' => Transaction::whereIn('m_id', $merchants->pluck('id'))
                ->where('type', 'in')
                ->where('confirmed', true)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount')
        ];

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentTransactions' => $recentTransactions,
            'monthlyStats' => $monthlyStats,
        ]);
    }


    public function transactions()
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);


        return Inertia::render('Dashboard/Transactions', [
            'transactions' => $transactions
        ]);
    }

    public function transaction($id)
    {
        $transaction = Transaction::find($id);

        return view('user.transaction', ['transaction' => $transaction]);
    }


    public function partners()
    {
        return Inertia::render('Dashboard/Partners');
    }
}
