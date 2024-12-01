<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use App\Models\Merchant;
use App\Models\PaymentSystem;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\SignatureService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private SignatureService $signatureService
    ) {}

    public function index(Request $request)
    {
        $merchant = $this->getApprovedMerchant($request);
        if (!$merchant) {
            return response()->json(['error' => 'Merchant not found'], 404);
        }

        $sessionData = $request->only('order', 'amount', 'currency', 'user_identify');
        $request->session()->put(['data' => $sessionData]);

        $paymentSystems = PaymentSystem::where('currency', $sessionData['currency'])
            ->where('activated', true)
            ->get();

        return view('api.index', [
            'data' => (object)$sessionData,
            'paymentSystems' => $paymentSystems,
            'shop' => $merchant,
        ]);
    }

    public function pay(Request $request)
    {
        try {
            $request->validate(['payment_system' => ['required']]);

            $data = (object)$request->session()->get('data');
            if (!$data) {
                return response()->json(['error' => 'Payment data not found'], 422);
            }

            $paymentSystem = PaymentSystem::findOrFail($request->get('payment_system'));

            // Создаем платеж
            $payment = $this->paymentService->initializePayment([
                'merchant' => $request->session()->get('shop'),
                'amount' => $data->amount,
                'currency' => $data->currency,
                'order' => $data->order,
                'user_identify' => $data->user_identify ?? null,
            ], $paymentSystem);

            if ($paymentSystem->provider) {
                // Для автоматических платежей возвращаем URL для оплаты
                return response()->json([
                    'payment_id' => $payment->id,
                    'redirect_url' => $payment->payment_url
                ]);
            }

            // Для ручных платежей показываем форму
            return view('api.pay', [
                'paymentSystem' => $paymentSystem,
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            Log::error('Payment creation failed', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Payment creation failed'], 500);
        }
    }

    public function status(Request $request, Payment $payment)
    {
        // Проверяем подпись запроса
        if (!$this->signatureService->verifySignature(
            ['payment_id' => $payment->id],
            $request->get('signature'),
            $payment->merchant->m_key
        )) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return response()->json([
            'status' => $payment->status,
            'payment_id' => $payment->id,
            'order' => $payment->order
        ]);
    }

    private function getApprovedMerchant(Request $request)
    {
        return Merchant::approvedAndActivated()
            ->where('m_id', $request->session()->get('shop'))
            ->first();
    }
}
