<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\Factories\PaymentProviderFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TestPaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Показ тестовой формы оплаты
     */
    public function showForm(Request $request)
    {
        $payment = Payment::where('external_id', $request->external_id)->firstOrFail();

        return view('payments.test-form', [
            'payment' => $payment,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'return_url' => route('api.webhook', ['provider' => 'test'])
        ]);
    }

    /**
     * Обработка тестового платежа
     */
    public function process(Request $request)
    {
        try {
            $payment = Payment::where('external_id', $request->external_id)->firstOrFail();

            // Получаем тестовый провайдер
            $provider = PaymentProviderFactory::create('test');

            // Обрабатываем платеж
            $result = $provider->processPayment($request);

            // Обновляем статус платежа
            $payment->update([
                'status' => $result['status'],
                'processed_at' => now()
            ]);

            // Отправляем вебхук мерчанту
            $this->paymentService->sendWebhook($payment);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message']
            ]);
        } catch (\Exception $e) {
            Log::error('Test payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment processing failed'
            ], 500);
        }
    }
}
