<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Payment;
use App\Models\ClientResponse;
use App\Models\PaymentSystem;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private Client $client;
    private SignatureService $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->client = new Client();
        $this->signatureService = $signatureService;
    }

    /**
     * Отправка асинхронного запроса мерчанту (старая функциональность)
     */
    public function sendAsyncRequest($transaction): void
    {
        $postData = $this->mappingResponseData($transaction);
        $postData['signature'] = $this->signatureService->generateSignature(
            $postData,
            $transaction->merchant->m_key
        );

        $postUrl = $transaction->merchant->handler_url;

        $promise = $this->client->postAsync($postUrl, ['form_params' => $postData])->then(
            function ($response) use ($postUrl, $transaction) {
                ClientResponse::updateOrCreate(
                    ['transaction_id' => $transaction->id],
                    ['data' => json_encode($response->getBody()->getContents())]
                );
                Log::info("Успешный асинхронный запрос ({$postUrl}): " . $response->getBody());
            },
            function ($exception) use ($postUrl, $transaction) {
                ClientResponse::updateOrCreate(
                    ['transaction_id' => $transaction->id],
                    ['data' => json_encode($exception->getMessage())]
                );
                Log::error("Ошибка при отправке асинхронного запроса ({$postUrl}): " . $exception->getMessage());
            }
        );

        $promise->wait();
    }


    private function mappingResponseData($transaction): array
    {
        $payment = $transaction->payment()->first();

        return [
            'status' => 'success',
            'operation_id' => $transaction->id,
            'operation_pay_system' => $payment->payment_system,
            'operation_date' => $transaction->created_at->format('Y-m-d H:i:s'),
            'operation_pay_date' => $transaction->updated_at->format('Y-m-d H:i:s'),
            'shop' => $payment->m_id,
            'order' => $payment->order,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'shop_key' => $payment->merchant->m_key,
        ];
    }


    /**
     * Создание платежа и инициализация в платежной системе
     */
    public function initializePayment(array $data, PaymentSystem $paymentSystem): Payment
    {
        try {
            // Создаем платеж в нашей системе
            $payment = Payment::create([
                'm_id' => $data['merchant']->m_id,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'order' => $data['order'],
                'user_identify' => $data['user_identify'] ?? null,
                'payment_system' => $paymentSystem->id,
                'status' => Payment::STATUS_PENDING
            ]);

            // Инициализируем платеж в платежной системе
            $providerResponse = $this->initializeProviderPayment($payment, $paymentSystem);

            // Сохраняем external_id
            $payment->update([
                'external_id' => $providerResponse['payment_id']
            ]);

            return $payment;
        } catch (\Exception $e) {
            Log::error('Payment initialization failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Отправка вебхука мерчанту с переотправкой при ошибке
     */
    public function sendWebhook(Payment $payment, int $retries = 3): void
    {
        $merchant = $payment->merchant;
        $webhookData = $this->prepareWebhookData($payment);

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $response = $this->client->post($merchant->webhook_url, [
                    'json' => $webhookData,
                    'headers' => [
                        'X-Signature' => $this->signatureService->generateSignature($webhookData, $merchant->webhook_secret),
                        'X-Attempt' => $attempt
                    ],
                    'timeout' => 5
                ]);

                if ($response->getStatusCode() === 200) {
                    // Сохраняем ответ
                    ClientResponse::create([
                        'payment_id' => $payment->id,
                        'data' => json_encode($response->getBody()->getContents())
                    ]);

                    Log::info('Webhook sent successfully', [
                        'payment_id' => $payment->id,
                        'attempt' => $attempt
                    ]);
                    return;
                }
            } catch (\Exception $e) {
                Log::warning("Webhook attempt {$attempt} failed", [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);

                if ($attempt === $retries) {
                    // Сохраняем ошибку
                    ClientResponse::create([
                        'payment_id' => $payment->id,
                        'data' => json_encode($e->getMessage())
                    ]);

                    throw $e;
                }

                // Ждем перед следующей попыткой
                sleep(pow(2, $attempt));
            }
        }
    }

    private function prepareWebhookData(Payment $payment): array
    {
        return [
            'payment_id' => $payment->id,
            'external_id' => $payment->external_id,
            'merchant_id' => $payment->merchant->m_id,
            'order' => $payment->order,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'timestamp' => now()->timestamp
        ];
    }

    private function initializeProviderPayment(Payment $payment, PaymentSystem $paymentSystem): array
    {
        // Здесь будет логика работы с конкретным провайдером
        // Пока возвращаем тестовые данные
        return [
            'payment_id' => 'test_' . uniqid(),
            'status' => 'pending'
        ];
    }
}
