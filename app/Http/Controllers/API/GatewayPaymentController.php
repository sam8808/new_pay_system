<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Services\FraudMonitoringService;

class GatewayPaymentController extends Controller
{
    protected $fraudMonitoring;

    public function __construct(FraudMonitoringService $fraudMonitoring)
    {
        $this->fraudMonitoring = $fraudMonitoring;
    }

    public function createPay(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency_id' => 'required|numeric',
            'description' => 'required|string',
            'gateway_id' => 'required|numeric',
            'merchant_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $uuid = Str::uuid();

        $merchant = Merchant::where('merchant_id', $data['merchant_id'])->firstOrFail();
        $payment = Payment::create([
            'uuid' => $uuid,
            'merchant_id' => $merchant->id,
            'payment_system_id' => $data['gateway_id'] ?? 1,
            'currency_id' => $data['currency_id'] ?? 2,
            'order_id' => $uuid,
            'amount' => $data['amount'],
            'processing_fee' => 1,
            'amount_in_base_currency' => $data['amount'] * 1.1,
            'status' => Payment::STATUS_PENDING_STRING,
            'external_id' => $uuid,
            'payer_email' => $request->input('payer_email', 'istore@mail.ru'),
            'payer_phone' => $request->input('payer_phone', '+78888884545'),
            'metadata' => $request->input('metadata', ''),
            'expires_at' => now()->addMinutes(30),
            'processed_at' => now(),
        ]);

        $wallet = $merchant->user->wallets()->where('currency_id', $payment->currency_id)->firstOrFail();
        // Fraud monitoring before creating a transaction
        if ($this->fraudMonitoring->analyzeTransaction((object) [
            'user_id' => $merchant->user_id,
            'amount' => $payment->amount,
        ])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction flagged as potential fraud.',
            ], 403);
        }

        // Create the transaction after passing fraud checks
        $transaction = Transaction::create([
            'uuid' => $payment->uuid,
            'user_id' => $merchant->user_id,
            'm_id' => $merchant->id,
            'amount' => $payment->amount,
            'amount_in_base_currency' => $payment->amount_in_base_currency,
            'currency_id' => $payment->currency_id,
            'type' => 'deposit',
            'wallet_id' => $wallet->id,
        ]);


        // Gateway processing
        $gateway = new GatewayController();
        $response = $gateway->processPayment([
            'gateway_id' => $data['gateway_id'],
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency_id,
            'description' => $data['description'],
        ]);

        return response()->json($response, 200);
    }
}
