<?php

namespace App\Services;

use Illuminate\Http\Request;

class SignatureService
{
    /**
     * Проверка подписи запроса от мерчанта
     */
    public function verifySignature(array $data, string $signature, string $merchantKey): bool
    {
        $expectedSignature = $this->generateSignature($data, $merchantKey);
        return hash_equals(strtoupper($expectedSignature), strtoupper($signature));
    }

    /**
     * Проверка подписи вебхука
     */
    public function verifyWebhookSignature(Request $request, array $config): bool
    {
        $signature = $request->header('X-Webhook-Signature');
        if (!$signature) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $config['webhook_secret']);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Генерация подписи для данных
     */
    public function generateSignature(array $data, string $secretKey): string
    {
        // Удаляем signature из данных если она есть
        unset($data['signature']);

        // Сортируем ключи для консистентности
        ksort($data);

        // Собираем строку для подписи
        $signString = '';
        foreach ($data as $value) {
            $signString .= $value . ':';
        }
        $signString .= $secretKey;

        return strtoupper(hash('sha256', $signString));
    }
}
