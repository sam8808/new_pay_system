<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $merchants = Merchant::where('user_id', Auth::user()->id)->get();
        $orders = collect();

        foreach ($merchants as $merchant) {
            $orders = $orders->concat($merchant->transactions);
        }

        return view('user.orders', ['orders' => $orders]);
    }
}
