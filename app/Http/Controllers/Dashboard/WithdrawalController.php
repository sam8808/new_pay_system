<?php

namespace App\Http\Controllers\Dashboard;

use Inertia\Inertia;
use App\Models\Merchant;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentSystem;
use App\Services\ExchangeService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class WithdrawalController extends Controller
{

    public function index()
    {
        return 'ok';
    }

    public function create()
    {
        $user = Auth::user();
        $wallets = $user->wallets()->where('balance', '>', 0)->with('currency')->get();
        $paymentSystems = PaymentSystem::where('is_active', true)
            ->where('type', 'both')
            ->with('currency')
            ->get();

        return Inertia::render('Dashboard/Withdrawal/Create', [
            'wallets' => $wallets->toArray(),
            'paymentSystems' => $paymentSystems,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric'],
            'm_id' => ['required'],
            'details' => ['required'],
            'ps_id' => ['required'],
        ]);


        $merchant = Merchant::find($request->post('m_id'));
        $paymentSystem = PaymentSystem::find($request->post('ps_id'));


        if ($merchant->balance < $request->post('amount')) {
            return back()->with(['message' => __('Недостаточно средств на балансе')]);
        }

        $merchant->balance -= $request->post('amount');
        $merchant->save();

        $currencies = config('payment.currencies');
        $currentCurrency = $paymentSystem->currency;
        $defaultCurrency = config('payment.default_currency.name');
        $amountDefaultCurrency = $request->post('amount');
        $amountCurrentCurrency = $amountDefaultCurrency;

        if ($currentCurrency !== $defaultCurrency) {
            $type = collect($currencies)->filter(function ($currencyArray) use ($currentCurrency) {
                return in_array($currentCurrency, $currencyArray);
            })->keys()->first();

            $amountUSD = $amountDefaultCurrency;

            if ($currentCurrency !== 'USD' && $currentCurrency !== 'USDT') {
                $amountUSD = ExchangeService::fromUSD($amountDefaultCurrency, $currentCurrency, $type);
            }
            $amountCurrentCurrency = ExchangeService::fromDefaultCurrency($amountUSD);
        }


        $withdrawal = Withdrawal::create([
            'user_id' => Auth::user()->id,
            'payment_system' => $paymentSystem->id,
            'details' => $request->post('details'),
            'amount' => $amountCurrentCurrency,
            'amount_default_currency' => $amountDefaultCurrency,
            'currency' => $paymentSystem->currency,
        ]);


        $transaction = Transaction::create([
            'user_id' => $withdrawal->user_id,
            'm_id' => $merchant->id,
            'amount' => $withdrawal->amount,
            'currency' => $withdrawal->currency,
            'type' => 'payOut',
            'withdrawal_id' => $withdrawal->id,
        ]);

        return Redirect::back()->with(['success' => __('Запрос на вывод средств отправлен на модерацию')]);
    }
}
