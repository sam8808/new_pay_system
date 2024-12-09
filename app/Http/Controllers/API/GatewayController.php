<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    const GATEWAY_DEFAULT = 0;

    public function processPayment($request)
    {
        switch ($request['gateway_id']) {
            case self::GATEWAY_DEFAULT:
                return $this->getPaymentLink($request);
                break;
            
            default:
                // code...
                break;
        }
    }

    public function getPaymentLink($request)
    {
        // Отправка запроса к внешней системе
        // доработать потом с конкретной платежной системе, лог декабрь 9
        $response = Http::post(env('EXTERNAL_PAYMENT_SYSTEM_URL'), [
            'payment_id' => $payment->id,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
        ]);

        Log::info('GatewayController@processPayment', $response);

        return response()->json(['payment_link' => $response->json('payment_link')]);
    }
}
