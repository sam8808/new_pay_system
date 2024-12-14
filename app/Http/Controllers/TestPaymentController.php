<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TestPaymentController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('TestPayment', [
            'title' => 'Главная | ' . config('app.name'),
            'merchant_id' => $request->merchant_id,
            'order_id' => $request->order_id
        ]);
    }
}
