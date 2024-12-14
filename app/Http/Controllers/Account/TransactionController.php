<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)
            ->with(['transactionable', 'currency', 'wallet'])
            ->orderByDesc('created_at')
            ->paginate(10);


        return Inertia::render('Account/Transactions', [
            'transactions' => $transactions
        ]);
    }

    public function show($uuid)
    {
        $transaction = Transaction::where('uuid', $uuid)
            ->with(['transactionable', 'currency', 'wallet'])
            ->firstOrFail();

        return Inertia::render('Account/Transaction', [
            'transaction' => $transaction
        ]);
    }
}
