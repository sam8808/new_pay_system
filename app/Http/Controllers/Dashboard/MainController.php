<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Transaction;
use App\Http\Controllers\Controller;


class MainController extends Controller
{
    public function index()
    {
        return redirect()->route('history');
    }


    public function history()
    {
        $transactions = Transaction::where('user_id', auth()->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);


        return view('user.history', ['operations' => $transactions]);
    }

    public function transaction($id)
    {
        $transaction = Transaction::find($id);

        return view('user.transaction', ['transaction' => $transaction]);
    }
}
