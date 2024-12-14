<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSystem;
use App\Services\PaymentSystemService;
use Illuminate\Http\Request;

class PaymentSystemController extends Controller
{

    public function index()
    {
        $paySystems = PaymentSystem::orderBy('created_at', 'DESC')->get();

        return view('admin.ps.index', ['paySystems' => $paySystems]);
    }


    public function create()
    {
        $fiatCurrencies = config('payment.currencies.fiat');
        $cryptoCurrencies = config('payment.currencies.crypto');
        $currencies = [...$fiatCurrencies, ...$cryptoCurrencies];

        return view('admin.ps.create', ['currencies' => $currencies]);
    }


    public function store(Request $request)
    {
        $service = new PaymentSystemService($request);
        $service->validate()->create();

        return redirect()->route('admin.ps');
    }


    public function changeStatus($id)
    {
        $paySystem = PaymentSystem::find($id);
        $paySystem->activated = !$paySystem->activated;
        $paySystem->save();

        return back();
    }


    public function edit($id)
    {
        $paySystem = PaymentSystem::find($id);

        return view('admin.ps.edit', ['paySystem' => $paySystem]);
    }

    public function update(Request $request, $id)
    {
        $service = new PaymentSystemService($request);
        $service->update((int)$id);

        return back();
    }
}
