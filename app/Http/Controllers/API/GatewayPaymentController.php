<?php

namespace App\Http\Controllers\API;

use App\Models\GatewayPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class GatewayPaymentController extends Controller
{
    public function createPay(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'order_id' => 'required|string|unique:payments,order_id',
            'description' => 'required|string',
            'client_data' => 'required|array',
            'gateway_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);  // 422 is Unprocessable Entity
        }

        $payment = GatewayPayment::create([
            'merchant_id' => auth()->id(), 
            'order_id' => $validated['order_id'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'status' => GatewayPayment::STATUS_CREATED,
        ]);

        // Передать запрос шлюзу
        //создал базовый шлюз, на основе которого потом будут созданы 
        //отдельные шлюзы для платежных систем
        //планируется использовать патерн стратегию для общего интерфейса 
        $gateway = new GatewayController();

        $response = $gateway->processPayment([
            'gateway_id' => $validated['gateway_id'],
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
        ]);

        Log::info('GatewayPaymentController@createPay', $response);

        return response()->json($response->json());
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
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the payment',
            ], 500);
        }
    }   
}
