<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Log;

class CurrencyRateService
{
    public function __construct(
        private readonly CurrencyApiService $apiService
    ) {}


    public function getBaseCurrency(): Currency
    {
        return Currency::base()->firstOrFail();
    }

    public function convertToBaseCurrency(float $amount, Currency $fromCurrency): float
    {
        if ($fromCurrency->id === $this->getBaseCurrency()->id) {
            return $amount;
        }

          return $amount * $this->getCurrencyRate($fromCurrency);

        // return $amount * $fromCurrency->exchange_rate;
    }

    public function getExchangeRate(Currency $fromCurrency, Currency $toCurrency): float
    {
        $existingRate = ExchangeRate::where('from_currency_id', $fromCurrency->id)
            ->where('to_currency_id', $toCurrency->id)
            ->first();

        if ($existingRate) {
            return $existingRate->rate;
        }

        try {
            $rate = $this->fetchCurrentRate($fromCurrency, $toCurrency);

            ExchangeRate::create([
                'from_currency_id' => $fromCurrency->id,
                'to_currency_id' => $toCurrency->id,
                'rate' => $rate
            ]);

            return $rate;
        } catch (\Exception $e) {
            Log::error('Exchange rate fetch error', [
                'from' => $fromCurrency->code,
                'to' => $toCurrency->code,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function getCurrencyRate(Currency $currency): float
    {
        try {
            if($currency->type === 'crypto') {
                return $this->apiService->getCryptoRate($currency->code, 'USD');
            }
            
            return $this->apiService->getFiatRate($currency->code, 'USD');
        } catch(\Exception $e) {
            Log::error('Failed to get currency rate', [
                'currency' => $currency->code,
                'error' => $e->getMessage()
            ]);
            
            // Возвращаем сохраненный курс если API недоступен
            return $currency->exchange_rate;
        }
    }

    private function fetchCurrentRate(Currency $fromCurrency, Currency $toCurrency): float
    {
        if ($fromCurrency->type === 'crypto' || $toCurrency->type === 'crypto') {
            return $this->apiService->getCryptoRate(
                $fromCurrency->code,
                $toCurrency->code
            );
        }

        return $this->apiService->getFiatRate(
            $fromCurrency->code,
            $toCurrency->code
        );
    }

    public function updateAllRates(): void
    {
        $currencies = Currency::where('is_active', true)->get();
        $baseCurrency = $this->getBaseCurrency();

        foreach ($currencies as $currency) {
            if ($currency->id === $baseCurrency->id) continue;

            try {
                $this->getExchangeRate($currency, $baseCurrency);
            } catch (\Exception $e) {
                Log::error('Rate update failed', [
                    'currency' => $currency->code,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
