<?php

namespace App\Services\Providers;

use App\Models\Payment;
use App\Contracts\PaymentProviderInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

abstract class BaseProvider implements PaymentProviderInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function initializePayment(Payment $payment): array
    {
        // Базовая реализация, которую можно переопределить
        return [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => 'pending'
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        // Базовая реализация проверки подписи
        $signature = $request->header('X-Webhook-Signature');
        $payload = $request->getContent();

        return hash_equals(
            hash_hmac('sha256', $payload, $this->config['webhook_secret']),
            $signature
        );
    }

    public function processWebhookData(Request $request): array
    {
        // Базовая реализация обработки вебхука
        return [
            'payment_id' => $request->input('payment_id'),
            'status' => $request->input('status'),
            'transaction_id' => $request->input('transaction_id')
        ];
    }

    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['api_key'],
            'Content-Type' => 'application/json'
        ])->$method($endpoint, $data);
    }
}
