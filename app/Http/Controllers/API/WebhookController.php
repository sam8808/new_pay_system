<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    public function handle(Request $request)
    {
        try {
            // Проверяем подпись от платежной системы
            // if (!$this->verifyProviderSignature($request, $provider)) {
            //     Log::warning('Invalid webhook signature from provider', [
            //         'provider' => $provider,
            //         'payload' => $request->all()
            //     ]);
            //     return response()->json(['error' => 'Invalid signature'], 401);
            // }

            // Находим платеж по external_id
            $payment = Payment::where('external_id', $request->payment_id)->first();
            if (!$payment) {
                Log::error('Payment not found for webhook', [
                    'external_id' => $request->payment_id
                ]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            if($request->isSuccess){
                $payment->status = Payment::STATUS_COMPLETED_STRING;
            }else{
                $payment->status = Payment::STATUS_FAILED_STRING;
            }

            $payment->save();

            // Обновляем статус платежа и отправляем вебхук мерчанту
            //$this->paymentService->sendWebhook($payment);
            $this->sendWebhook($payment);

            return response()->json([
                'status' => 'ok', 
                'code' => 200, 
                'success' => $request->isSuccess,
                'message' => 'webhook processed'
            ]);
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


    public function sendWebhook($payment)
    {
        // The merchant's webhook URL
        $webhookUrl = $payment->merchant->webhook_url;

        // The data to be sent in the webhook payload
        $data = [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'processed_at' => $payment->processed_at,
            // Add any other payment or merchant-specific data you need to include
        ];

        try {
            // Send the POST request to the webhook URL
            //$response = Http::get($webhookUrl, $data);

            // Log the response for debugging
            // \Log::info('Webhook response', ['status' => $response->status(), 'response' => $response->body()]);

            // Optionally, handle different response statuses if needed
            // if ($response->failed()) {
            //     throw new \Exception('Webhook request failed');
            // }

            return true;

        } catch (\Exception $e) {
            // Log the error if something goes wrong
            dd($e->getMessage());
            \Log::error('Webhook error', ['message' => $e->getMessage()]);
        }
    }
}
