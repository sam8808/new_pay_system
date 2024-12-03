<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Services\MerchantService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class MerchantController extends Controller
{

    public function index()
    {
        $merchants = Merchant::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Inertia::render('Account/Merchant/Index', [
            'merchants' => $merchants
        ]);
    }



    public function show($merchant)
    {
        $merchant = Merchant::find($merchant);

        return Inertia::render('Account/Merchant/Show', [
            'merchant' => $merchant
        ]);
    }


    public function create()
    {
        return Inertia::render('Account/Merchant/Create');
    }


    public function store(Request $request)
    {
        $service = new MerchantService($request);
        $merchant = $service->validate()->create();

        return Redirect::route('merchant.show', [$merchant->id]);
    }



    public function edit($merchant)
    {
        $merchant = Merchant::find($merchant);

        return Inertia::render('Account/Merchant/Edit', [
            'merchant' => $merchant
        ]);
    }


    public function update(Request $request, $merchant)
    {
        $merchant = Merchant::find($merchant);
        $merchant->update($request->all());

        return back();
    }



    public function activateOrDeactivate($merchant)
    {
        $merchant = Merchant::find($merchant);
        $merchant->is_active = !$merchant->is_active;
        $merchant->save();

        return back();
    }


    public function destroy($merchant)
    {
        $merchant = Merchant::find($merchant);
        $merchant->delete();

        return redirect()->route('merchant');
    }
}
