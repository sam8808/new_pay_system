<?php

namespace App\Services\Providers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestProvider extends BaseProvider
{
    /**
     * Инициализация тестового платежа
     */
    public function initializePayment(Payment $payment): array
    {
        try {
            // Генерируем тестовый external_id
            $externalId = 'TEST_' . uniqid();

            // Создаем тестовую ссылку для оплаты
            $paymentUrl = route('test.payment.form', [
                'external_id' => $externalId,
                'amount' => $payment->amount,
                'currency' => $payment->currency
            ]);

            // Сохраняем external_id в платеж
            $payment->update([
                'external_id' => $externalId,
                'payment_url' => $paymentUrl
            ]);

            return [
                'payment_id' => $externalId,
                'payment_url' => $paymentUrl,
                'status' => Payment::STATUS_PENDING
            ];
        } catch (\Exception $e) {
            Log::error('Test payment initialization failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Обработка тестового платежа
     */
    public function processPayment(Request $request): array
    {
        $cardNumber = $request->input('card_number');

        // Симулируем различные ответы в зависимости от номера карты
        return match ($cardNumber) {
            '4242424242424242' => [
                'status' => Payment::STATUS_SUCCESS,
                'message' => 'Payment successful'
            ],
            '4000000000000002' => [
                'status' => Payment::STATUS_FAILED,
                'message' => 'Card declined'
            ],
            '4000000000009995' => [
                'status' => Payment::STATUS_FAILED,
                'message' => 'Insufficient funds'
            ],
            default => [
                'status' => Payment::STATUS_FAILED,
                'message' => 'Invalid card'
            ]
        };
    }

    /**
     * Обработка вебхука от тестовой системы
     */
    public function processWebhookData(Request $request): array
    {
        return [
            'payment_id' => $request->input('external_id'),
            'status' => $request->input('status'),
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
            'transaction_id' => 'TEST_TXN_' . uniqid(),
            'processed_at' => now()->toIso8601String()
        ];
    }
}
