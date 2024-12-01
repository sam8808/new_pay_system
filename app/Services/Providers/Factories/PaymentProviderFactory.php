<?php

namespace App\Services\Factories;

use App\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Log;

class PaymentProviderFactory
{
    /**
     * Создает экземпляр провайдера платежной системы
     */
    public static function create(string $provider): PaymentProviderInterface
    {
        $config = config("payment.providers.{$provider}");

        if (!$config || !isset($config['class'])) {
            Log::error('Payment provider configuration not found', ['provider' => $provider]);
            throw new \Exception("Provider {$provider} not found or misconfigured");
        }

        $class = $config['class'];

        if (!class_exists($class)) {
            Log::error('Payment provider class not found', ['class' => $class]);
            throw new \Exception("Provider class {$class} not found");
        }

        try {
            return new $class($config);
        } catch (\Exception $e) {
            Log::error('Failed to create payment provider', [
                'provider' => $provider,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
