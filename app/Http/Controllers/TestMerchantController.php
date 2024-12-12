<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TestMerchantController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('TestMerchant', [
            'title' => 'Главная | ' . config('app.name'),
            'merchant_id' => $request->merchant_id,
            'homePageRoute' => route('test.merchant', ['merchant_id' => $request->merchant_id]),
            'createCouponPageRoute' => route('merchant-coupon.create', ['merchant_id' => $request->merchant_id])
        ]);
    }

    public function webhook(Request $request)
    {
        return response()->json(['error' => 'Internal error'], 500);
    }
}
