<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MerchantCoupons;

class TinkoffGatewayController extends Controller
{
    /**
     * Generates a payment link using the Tinkoff API.
     *
     * @param Request $request The HTTP request containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the payment link or an error message.
     */
    public function getPaymentLink(Request $request)
    {
        $amount = $request->input('amount'); // Amount
        $orderId = $request->input('order_id'); // Unique order ID
        $description = $request->input('description'); // Optional description

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
            'NotificationURL' => route('tinkoff.webhook'), // Webhook route
            'SuccessURL' => route('tinkoff.success'), // Success route
            'FailURL' => route('tinkoff.fail'), // Failure route
        ];

        try {
            $response = Http::post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                if (!empty($responseData['Success']) && $responseData['Success']) {
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
        $data = $request->all();
        Log::info('Tinkoff webhook received:', $data);

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
            return response()->json(['status' => 'success']);
        }

        return response()->json(['error' => 'Invalid token.'], 403);
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
     * Processes a regular payment using the Tinkoff API.
     *
     * @param Request $request The HTTP request containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the payment status.
     */
    public function regularPayment(Request $request)
    {
        $amount = $request->input('amount');
        $orderId = $request->input('order_id');
        $description = $request->input('description', 'Regular payment');

        $terminalKey = env('TINKOFF_TERMINAL_KEY');
        $password = env('TINKOFF_PASSWORD');
        $url = 'https://securepay.tinkoff.ru/v2/Charge';

        $data = [
            'TerminalKey' => $terminalKey,
            'PaymentId' => $orderId,
            'Amount' => $amount,
            'Token' => hash('sha256', $terminalKey . $orderId . $amount . $password),
        ];

        try {
            $response = Http::post($url, $data);

            if ($response->successful()) {
                $responseData = $response->json();
                if (!empty($responseData['Success']) && $responseData['Success']) {
                    return response()->json([
                        'message' => 'Payment processed successfully.',
                        'order_id' => $orderId,
                        'amount' => $amount,
                        'status' => 'success',
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Payment failed.',
                        'error_message' => $responseData['Message'] ?? 'Unknown error',
                    ], 400);
                }
            } else {
                return response()->json(['error' => 'Failed to connect to Tinkoff API.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Tinkoff Regular Payment Error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during payment processing.'], 500);
        }
    }

    /**
     * Processes coupon-based payments by reusing the regular payment function.
     *
     * @param MerchantCoupons $coupon The coupon object containing payment details.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the coupon payment result.
     */
    public function couponPayment(MerchantCoupons $coupon)
    {
        return $this->regularPayment(new Request([
            'amount' => $coupon->amount,
            'order_id' => $coupon->payment->external_id,
            'description' => $coupon->payment->description ?? 'Payment from gateway',
        ]));
    }
}
