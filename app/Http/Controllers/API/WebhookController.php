<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\SignatureService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private SignatureService $signatureService
    ) {}

    public function handle(Request $request, string $provider)
    {
        try {
            // Проверяем подпись от платежной системы
            if (!$this->verifyProviderSignature($request, $provider)) {
                Log::warning('Invalid webhook signature from provider', [
                    'provider' => $provider,
                    'payload' => $request->all()
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Находим платеж по external_id
            $payment = Payment::where('external_id', $request->payment_id)->first();
            if (!$payment) {
                Log::error('Payment not found for webhook', [
                    'external_id' => $request->payment_id
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Обновляем статус платежа и отправляем вебхук мерчанту
            $this->paymentService->sendWebhook($payment);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    private function verifyProviderSignature(Request $request, string $provider): bool
    {
        // Тут будет логика проверки подписи от конкретного провайдера
        return true; // Временно для теста
    }
}
