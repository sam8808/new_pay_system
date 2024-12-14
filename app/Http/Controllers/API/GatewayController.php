<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class GatewayController
 * Handles payment processing by directing requests to the appropriate payment gateway.
 */
class GatewayController extends Controller
{
    /** @var int GATEWAY_DEFAULT Identifier for the default payment gateway. */
    const GATEWAY_DEFAULT = 1;

    /** @var int GATEWAY_TINKOFF Identifier for the Tinkoff payment gateway. */
    const GATEWAY_TINKOFF = 2;

    /** @var int GATEWAY_SBER Identifier for the Sber payment gateway. */
    const GATEWAY_SBER = 3;

    /** @var int GATEWAY_CRYPTOMUS Identifier for the Cryptomus payment gateway. */
    const GATEWAY_CRYPTOMUS = 4;

    /**
     * Process a payment request by delegating to the appropriate gateway.
     *
     * @param array $request The payment request data, including 'gateway_id' and other required details.
     * @return \Illuminate\Http\JsonResponse The response from the selected payment gateway, typically containing a payment link.
     */
    public function processPayment($request)
    {
        switch ($request['gateway_id']) {
            case self::GATEWAY_DEFAULT:
                return $this->getPaymentLink($request);
            case self::GATEWAY_TINKOFF:
                return (new TinkoffGatewayController())->getPaymentLink($request);
            case self::GATEWAY_SBER:
                return (new SberGatewayController())->getPaymentLink($request);
            case self::GATEWAY_CRYPTOMUS:
                return (new CryptomusGatewayController())->getPaymentLink($request);
            default:
                // Handle unsupported gateway ID
                return response()->json(['error' => 'Unsupported gateway ID'], 400);
        }
    }

    /**
     * Generate a mock payment link for the default payment gateway.
     * This simulates sending a request to an external payment system and generating a response.
     *
     * @param array $request The payment request data, including 'order_id' and 'payment_id'.
     * @return \Illuminate\Http\JsonResponse The generated payment link and associated data in JSON format.
     */
    public function getPaymentLink(array $request)
    {
        // Extract order ID and other details from the request
        $order_id = $request['order_id'];

        // Simulate a mock response from an external payment system
        $response = collect([
            'payment_link' => "/test-payment?order_id=$order_id",
            'order_id' => $order_id,
            'payment_id' => $request['payment_id']
        ]);

        // Log the simulated response (replace this with real logging for actual integration)
        Log::info('GatewayController@getPaymentLink', $response->toArray());

        // Return the response as JSON
        return response()->json($response);
    }
}
