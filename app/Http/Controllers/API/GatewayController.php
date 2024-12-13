<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    const GATEWAY_DEFAULT = 1;
    const GATEWAY_TINKOFF = 2;
    const GATEWAY_SBER = 3;

    public function processPayment($request)
    {
        switch ($request['gateway_id']) {
            case self::GATEWAY_DEFAULT:
                return $this->getPaymentLink($request);
                break;
            case self::GATEWAY_TINKOFF:
                return (new TinkoffGatewayController())->getPaymentLink($request);
                break;
            case self::GATEWAY_SBER:
                return (new SberGatewayController())->getPaymentLink($request);
                break;

            default:
                // code...
                break;
        }
    }

    public function getPaymentLink(array $request)
    {
        // Simulate the request to an external payment system
        // In a real scenario, you would send a request to the payment system and process the response
        // Example:
        // $response = Http::post(env('EXTERNAL_PAYMENT_SYSTEM_URL'), [
        //     'payment_id' => $payment->id,
        //     'amount' => $validated['amount'],
        //     'currency' => $validated['currency'],
        // ]);

        // Simulating a mock response
        $order_id = $request['order_id'];
        $response = collect([
            'payment_link' => "/test-payment?order_id=$order_id",
            'order_id' => $order_id,
            'payment_id' => $request['payment_id']
        ]);

        // Log the simulated response (in reality, you would log the actual response from the external system)
        //Log::info('GatewayController@processPayment', $response);

        // Return the payment link in a JSON response
        return response()->json($response);
    }
}
