<?php

namespace App\Http\Controllers\API;

use App\Models\GatewayPayment;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class GatewayPaymentController extends Controller
{
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
            ], 422);  // 422 is Unprocessable Entity
        }

        // Access validated data
        $data = $validator->validated();
        $uuid = Str::uuid();

        $payment = Payment::create([
            'uuid' => $uuid,
            'merchant_id' => Merchant::where('merchant_id', $data['merchant_id'])->first()->id,
            'payment_system_id' => $data['gateway_id'] ?? 1,
            'currency_id' => $data['currency_id'] ?? 2, //usd
            'order_id' => $uuid, // ID заказа в системе мерчанта
            'amount' => $data['amount'],
            'processing_fee' => 1,
            'amount_in_base_currency' => $data['amount'] * 1.1,
            'status' => Payment::STATUS_PENDING_STRING,
            'external_id' =>  $uuid,
            'payer_email' => 'istore@mail.ru',
            'payer_phone' => '+78888884545',
            'metadata' => '',
            'expires_at' => time(),
            'processed_at' => time()
        ]);

        $wallet = $payment->merchant->user->wallets()->where('currency_id', $payment->currency_id)->first();

        $transaction = Transaction::create([
            'uuid' => $payment->uuid,
            'user_id' => $payment->merchant->user_id,
            'm_id' => $payment->merchant->id,
            'amount' => $payment->amount,
            'amount_in_base_currency' => $payment->amount_in_base_currency,
            'currency_id' => $payment->currency_id,
            'type' => 'deposit',
            'wallet_id' => $wallet->id
        ]);

        // Передать запрос шлюзу
        //создал базовый шлюз, на основе которого потом будут созданы
        //отдельные шлюзы для платежных систем
        //планируется использовать патерн стратегию для общего интерфейса
        $gateway = new GatewayController();

        $response = $gateway->processPayment([
            'gateway_id' => $data['gateway_id'],
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency_id,
            'description'=> $data['description']
        ]);

        //Log::info('GatewayPaymentController@createPay', $response);

        return  Response::json($response, 200);

    }

    public function getPayment(Request $request, $id)
    {
        try {
            // Attempt to retrieve the payment by ID
            $payment = GatewayPayment::findOrFail($id);

            // Log the retrieved payment details
            Log::info('GatewayPaymentController@getPayment', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
            ]);

            // Return the payment details as JSON
            return response()->json([
                'success' => true,
                'data' => $payment,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Log the error if the payment is not found
            Log::error('GatewayPaymentController@getPayment - Payment not found', [
                'payment_id' => $id,
            ]);

            // Return a not found response
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        } catch (\Exception $e) {
            // Log any other exceptions
            Log::error('GatewayPaymentController@getPayment - Error', [
                'error' => $e->getMessage(),
            ]);

            // Return a general error response
            return  Response::json([
                'success' => false,
                'message' => 'An error occurred while retrieving the payment',
            ], 500);
        }
    }
}
