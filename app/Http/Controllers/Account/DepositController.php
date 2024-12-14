<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use App\Models\Currency;
use App\Services\DepositService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class DepositController extends Controller
{
    public function __construct(
        private readonly DepositService $depositService
    ) {}

    public function index()
    {
        return Inertia::render('Account/Deposits/Index', [
            'deposits' => $this->depositService->getUserDeposits(),
            'paymentSystems' => $this->depositService->getAvailablePaymentSystems(),
            'currencies' => Currency::where('is_active', true)->get(),
        ]);
    }


    public function store()
    {
        $deposit = $this->depositService
            ->validate()
            ->create();

        return Redirect::route('deposit');
    }



    public function show(string $uuid)
    {
        return Inertia::render('Account/Deposits/Show', [
            'deposit' => $this->depositService->getDepositByUuid($uuid)
        ]);
    }
}
