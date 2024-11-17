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
        return Inertia::render('Dashboard/Index');
    }


    public function history()
    {
        $transactions = Transaction::where('user_id', Auth::user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);


        return Inertia::render('Dashboard/History', [
            'operations' => $transactions
        ]);
    }

    public function transaction($id)
    {
        $transaction = Transaction::find($id);

        return view('user.transaction', ['transaction' => $transaction]);
    }
}
