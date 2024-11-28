<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TestPaymentController extends Controller
{
    public function create(Request $request)
    {
        Log::info('Test payment creation:', $request->all());

        return response()->json([
            'id' => 'TEST_' . uniqid(),
            'status' => 'initialized',
            'test_mode' => true
        ]);
    }


    public function process(Request $request, $paymentId)
    {
        Log::info('Test payment processing:', [
            'payment_id' => $paymentId,
            'data' => $request->all()
        ]);

        // Симулируем различные ответы в зависимости от номера карты
        $cardNumber = $this->decryptCardNumber($request->input('payment_data'));

        switch ($cardNumber) {
            case '4242424242424242':
                return response()->json([
                    'status' => 'success',
                    'transaction_id' => 'TEST_TXN_' . uniqid()
                ]);

            case '4000000000000002':
                return response()->json([
                    'status' => 'error',
                    'error' => 'Card declined'
                ], 422);

            case '4000000000009995':
                return response()->json([
                    'status' => 'error',
                    'error' => 'Insufficient funds'
                ], 422);

            default:
                return response()->json([
                    'status' => 'error',
                    'error' => 'Invalid card'
                ], 422);
        }
    }

    private function decryptCardNumber($encryptedData)
    {
        try {
            // Для тестового окружения просто декодируем base64
            // и извлекаем номер карты из JSON
            $decodedData = base64_decode($encryptedData);
            $paymentData = json_decode($decodedData, true);

            return $paymentData['cardNumber'] ?? '0000000000000000';
        } catch (\Exception $e) {
            Log::error('Test decryption error: ' . $e->getMessage());
            return '0000000000000000';
        }
    }
}