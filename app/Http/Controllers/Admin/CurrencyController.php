<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\CurrencyService;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Support\Facades\Redirect;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::paginate(30);

        return view('admin.currency.index', [
            'currencies' => $currencies
        ]);
    }

    public function create()
    {
        return view('admin.currency.create');
    }

    public function store(Request $request)
    {
        $service = new CurrencyService($request);
        $service->validate()->create();

        return Redirect::route('admin.currency');
    }

    public function changeStatus($currency)
    {
        $currency = Currency::findOrFail($currency);
        $currency->status = !$currency->status;
        $currency->save();

        return back();
    }
}
