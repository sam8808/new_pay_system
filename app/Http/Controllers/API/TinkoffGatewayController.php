<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TinkoffGatewayController extends Controller
{
    /**
     * Generates a payment link using the Tinkoff API.
     *
     * @param Request $request The HTTP request containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the payment link or an error message.
     */
    public function getPaymentLink(array $request)
    {
        $amount = $request['amount']; // Amount
        $orderId = $request['order_id']; // Unique order ID
        $description = $request['description']; // Optional description

        // Tinkoff API credentials
        $terminalKey = env('TINKOFF_TERMINAL_KEY');
        $password = env('TINKOFF_PASSWORD');

        // API endpoint
        $url = 'https://securepay.tinkoff.ru/v2/Init';

        // Prepare the data for the API request
        $data = [
            'TerminalKey' => $terminalKey,
            'Amount' => $amount,
            'OrderId' => $orderId,
            'Description' => $description,
            'NotificationURL' => route('tinkoff.webhook'), //  webhook route
            'SuccessURL' => route('tinkoff.success'), //  success route
            'FailURL' => route('tinkoff.fail'), //  failure route
        ];

        // Make the HTTP POST request to Tinkoff API
        try {
            $response = Http::post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['Success']) && $responseData['Success']) {
                    return response()->json([
                        'payment_link' => $responseData['PaymentURL'], // Payment link
                    ]);
                } else {
                    return response()->json([
                        'error' => $responseData['Message'] ?? 'Failed to create payment link.',
                    ], 400);
                }
            } else {
                return response()->json(['error' => 'Failed to connect to Tinkoff API.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Tinkoff API error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /**
     * Handles webhook notifications from Tinkoff.
     *
     * @param Request $request The HTTP request containing the webhook payload.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the webhook handling result.
     */
    public function webhook(Request $request)
    {
        // Process Tinkoff webhook notification
        $data = $request->all();

        // Log the webhook data for debugging
        Log::info('Tinkoff webhook received:', $data);

        // Validate the webhook (e.g., Token validation can be implemented here)
        $calculatedToken = hash('sha256', implode('', [
            env('TINKOFF_PASSWORD'),
            $data['TerminalKey'] ?? '',
            $data['OrderId'] ?? '',
            $data['Success'] ?? '',
            $data['Status'] ?? '',
            $data['PaymentId'] ?? '',
            $data['Amount'] ?? '',
        ]));

        if (($data['Token'] ?? '') === $calculatedToken) {
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
