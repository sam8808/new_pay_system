<?php

namespace App\Http\Controllers\Dashboard;

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

        return Inertia::render('Dashboard/Merchant/Index', [
            'merchants' => $merchants
        ]);
    }



    public function show($id)
    {
        $merchant = Merchant::find($id);

        return Inertia::render('Dashboard/Merchant/Show', [
            'merchant' => $merchant
        ]);
    }


    public function create()
    {
        return Inertia::render('Dashboard/Merchant/Create');
    }


    public function store(Request $request)
    {
        $service = new MerchantService($request);
        $merchant = $service->validate()->create();

        return Redirect::route('merchant.show', [$merchant->id]);
    }



    public function edit($id)
    {
        $merchant = Merchant::find($id);

        return Inertia::render('Dashboard/Merchant/Edit', [
            'merchant' => $merchant
        ]);
    }


    public function update(Request $request, $id)
    {
        $merchant = Merchant::find($id);
        $merchant->update($request->all());

        return back();
    }



    public function activateOrDeactivate($id)
    {
        $merchant = Merchant::find($id);
        $merchant->activated = !$merchant->activated;
        $merchant->save();

        return back();
    }


    public function destroy($id)
    {
        $merchant = Merchant::find($id);
        $merchant->delete();

        return redirect()->route('merchant');
    }
}
