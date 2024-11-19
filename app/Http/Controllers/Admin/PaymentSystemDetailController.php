<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\PaymentSystem;
use App\Models\PaymentSystemDetail;
use App\Http\Controllers\Controller;
use App\Services\PaymentSystemService;


class PaymentSystemDetailController extends Controller
{

    public function index()
    {
        $PaymentSystemDetails = PaymentSystemDetail::query()
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('admin.ps.info.index', ['PaymentSystemDetails' => $PaymentSystemDetails]);
    }


    public function show($id)
    {
        $paySystem = PaymentSystem::find($id);

        return view('admin.ps.info.show', ['paySystem' => $paySystem]);
    }


    public function create()
    {
        $paySystems = PaymentSystem::all();

        return view('admin.ps.info.create', ['paySystems' => $paySystems]);
    }


    public function edit($id)
    {
        $PaymentSystemDetail = PaymentSystemDetail::find($id);

        return view('admin.ps.info.edit', ['PaymentSystemDetail' => $PaymentSystemDetail]);
    }


    public function update(Request $request, $id)
    {
        $PaymentSystemDetail = PaymentSystemDetail::find($id);
        $PaymentSystemDetail->update($request->all());

        return redirect()->route('admin.ps.info.show', [$PaymentSystemDetail->paymentSystem->id]);
    }


    public function store(Request $request)
    {
        $service = new PaymentSystemService($request);
        $service->validateInfo()->createDetail();

        return redirect()->route('admin.ps.info');
    }


    public function change($id)
    {
        $PaymentSystemDetail = PaymentSystemDetail::find($id);
        $PaymentSystemDetail->activated = !$PaymentSystemDetail->activated;
        $PaymentSystemDetail->save();

        return back();
    }


    public function destroy($id)
    {
        $PaymentSystemDetail = PaymentSystemDetail::find($id);
        $PaymentSystemDetail->delete();

        return back();
    }
}
