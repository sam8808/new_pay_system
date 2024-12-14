<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SberGatewayController extends Controller
{
    /**
     * Generates a payment link using the SberBank API.
     *
     * @param Request $request The HTTP request containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the payment link or an error message.
     */
    public function getPaymentLink(array $request)
    {
        $amount = $request['amount']; // Amount
        $orderId = $request['order_id']; // Unique order ID
        $description = $request['description']; // Optional description

        // SberBank API credentials
        $login = env('SBER_LOGIN');
        $password = env('SBER_PASSWORD');

        // API endpoint
        $url = env('SBER_API_URL') . 'register.do';

        // Prepare the data for the API request
        $data = [
            'userName' => $login,
            'password' => $password,
            'orderNumber' => $orderId,
            'amount' => $amount,
            'returnUrl' => route('sber.success'), //  success route
            'failUrl' => route('sber.fail'), //  failure route
            'jsonParams' => json_encode([
                'callbackUrl' => route('sber.webhook'), //  webhook route
            ]),
        ];

        // Make the HTTP POST request to SberBank API
        try {
            $response = Http::post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['formUrl'])) {
                    return response()->json([
                        'payment_link' => $responseData['formUrl'], // Payment link
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Failed to create payment link.',
                    ], 400);
                }
            } else {
                return response()->json(['error' => 'Failed to connect to SberBank API.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('SberBank API error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /**
     * Handles webhook notifications from SberBank.
     *
     * @param Request $request The HTTP request containing the webhook payload.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the webhook handling result.
     */
    public function webhook(Request $request)
    {
        // Process SberBank webhook notification
        $data = $request->all();

        // Log the webhook data for debugging
        Log::info('SberBank webhook received:', $data);

        // Validate the webhook (e.g., Token validation can be implemented here)
        $calculatedToken = hash('sha256', implode('', [
            env('SBER_PASSWORD'),
            $data['orderNumber'] ?? '',
            $data['status'] ?? '',
            $data['amount'] ?? '',
        ]));

        if (($data['token'] ?? '') === $calculatedToken) {
            // Process valid webhook data (e.g., update order status)
            return response()->json(['status' => 'success']);
        }

        // Invalid webhook request
        return response()->json(['error' => 'Invalid token.'], 403);
    }

    /**
     * Displays a success message for successful payments.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with a success message.
     */
    public function success()
    {
        // Payment success handler
        return response()->json(['message' => 'Payment successful!']);
    }

    /**
     * Displays a failure message for failed payments.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with a failure message.
     */
    public function fail()
    {
        // Payment failure handler
        return response()->json(['message' => 'Payment failed.']);
    }
}
