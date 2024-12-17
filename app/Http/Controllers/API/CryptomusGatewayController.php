<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MerchantCoupons;

class CryptomusGatewayController extends Controller
{
    /**
     * Generates a payment link using the Cryptomus API.
     *
     * @param array $request The HTTP request containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the payment link or an error message.
     */
    public function getPaymentLink(array $request)
    {
        $amount = $request['amount']; // Amount in the specified cryptocurrency
        $orderId = $request['order_id']; // Unique order ID
        $currency = $request['currency']; // Cryptocurrency (e.g., BTC, ETH, USDT)
        $description = $request['description'] ?? 'Payment via Cryptomus'; // Optional description

        // Cryptomus API credentials
        $merchantKey = env('CRYPTOMUS_MERCHANT_KEY');
        $secretKey = env('CRYPTOMUS_SECRET_KEY');

        // API endpoint
        $url = 'https://api.cryptomus.com/v1/payment';

        // Prepare the data for the API request
        $data = [
            'amount' => $amount,
            'order_id' => $orderId,
            'currency' => $currency,
            'description' => $description,
            'callback_url' => route('cryptomus.webhook'), // Webhook route
            'success_url' => route('cryptomus.success'), // Success route
            'fail_url' => route('cryptomus.fail'), // Failure route
        ];

        // Generate the signature (HMAC-SHA256)
        $signature = hash_hmac('sha256', json_encode($data), $secretKey);

        // Add signature to the headers
        $headers = [
            'merchant' => $merchantKey,
            'sign' => $signature,
        ];

        // Make the HTTP POST request to Cryptomus API
        try {
            $response = Http::withHeaders($headers)->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['result']['url'])) {
                    return response()->json([
                        'payment_link' => $responseData['result']['url'], // Payment link
                    ]);
                } else {
                    return response()->json([
                        'error' => $responseData['error']['message'] ?? 'Failed to create payment link.',
                    ], 400);
                }
            } else {
                return response()->json(['error' => 'Failed to connect to Cryptomus API.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Cryptomus API error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /**
     * Handles webhook notifications from Cryptomus.
     *
     * @param Request $request The HTTP request containing the webhook payload.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the webhook handling result.
     */
    public function webhook(Request $request)
    {
        $data = $request->all();

        // Log the webhook data for debugging
        Log::info('Cryptomus webhook received:', $data);

        // Validate the webhook signature
        $providedSignature = $request->header('sign');
        $calculatedSignature = hash_hmac('sha256', json_encode($data), env('CRYPTOMUS_SECRET_KEY'));

        if ($providedSignature === $calculatedSignature) {
            // Process valid webhook data (e.g., update order status)
            return response()->json(['status' => 'success']);
        }

        // Invalid webhook request
        return response()->json(['error' => 'Invalid signature.'], 403);
    }

    /**
     * Displays a success message for successful payments.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with a success message.
     */
    public function success()
    {
        return response()->json(['message' => 'Payment successful!']);
    }

    /**
     * Displays a failure message for failed payments.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with a failure message.
     */
    public function fail()
    {
        return response()->json(['message' => 'Payment failed.']);
    }

    /**
     * Handles regular payments through the Cryptomus API.
     *
     * @param array $request The HTTP request containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the payment link or error message.
     */
    public function regularPayment(array $request)
    {
        $amount = $request['amount'];
        $currency = $request['currency'];
        $orderId = $request['order_id'];
        $description = $request['description'] ?? 'Regular Payment';

        // Cryptomus API credentials
        $merchantKey = env('CRYPTOMUS_MERCHANT_KEY');
        $secretKey = env('CRYPTOMUS_SECRET_KEY');
        $url = 'https://api.cryptomus.com/v1/payment';

        $data = [
            'amount' => $amount,
            'order_id' => $orderId,
            'currency' => $currency,
            'description' => $description,
            'callback_url' => route('cryptomus.webhook'),
        ];

        $signature = hash_hmac('sha256', json_encode($data), $secretKey);
        $headers = ['merchant' => $merchantKey, 'sign' => $signature];

        try {
            $response = Http::withHeaders($headers)->post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                return response()->json([
                    'payment_link' => $responseData['result']['url'] ?? '',
                ]);
            }
            return response()->json(['error' => 'Failed to process regular payment.'], 400);
        } catch (\Exception $e) {
            Log::error('Regular Payment Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing regular payment.'], 500);
        }
    }

    public function couponPayment(MerchantCoupons $coupon)
    {
        return $this->regularPayment([
            'amount' => $coupon->amount,
            'currency' => $coupon->payment->currency_id,
            'order_id' => $coupon->payment->external_id,
            'description' => $coupon->payment->description ?? 'Regular Payment', // Fixed the semicolon issue
        ]);
    }

}
