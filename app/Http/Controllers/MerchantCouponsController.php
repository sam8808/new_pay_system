<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMerchantCouponsRequest;
use App\Http\Requests\UpdateMerchantCouponsRequest;
use App\Models\MerchantCoupons;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class MerchantCouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('TestMerchantCoupon', [
            'title' => 'Главная | ' . config('app.name'),
            'merchant_id' => $request->merchant_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Генерация купона
     *   Создание платежа в статусе pending
     *   Генерация уникального кода купона (6-8 символов)
     *   Отправка данных купона мерчанту
     *   Сохранение информации в БД
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
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
        $couponCode = substr(Str::uuid()->toString(), 0, 16);

        $payment = Payment::create([
            'uuid' => $uuid,
            'merchant_id' => $data['merchant_id'] ?? null, // Use $data['merchant_id'] or fallback to null
            'payment_system_id' => $data['gateway_id'] ?? 1, // Fallback to 1 if not provided
            'currency_id' => $data['currency_id'] ?? 2, // Fallback to 2 (USD) if not provided
            'order_id' => $uuid, // ID заказа в системе мерчанта
            'amount' => $data['amount'] ?? 0, // Fallback to 0 if 'amount' is not provided
            'processing_fee' => $data['processing_fee'] ?? 1, // Fallback to 1 if not provided
            'amount_in_base_currency' => ($data['amount'] ?? 0) * 1.1, // Use 1.1 multiplier on amount or 0
            'status' => $data['status'] ?? Payment::STATUS_PENDING_STRING, // Use $data['status'] or fallback to pending
            'external_id' => $uuid,
            'payer_email' => $data['payer_email'] ?? 'istore@mail.ru', // Fallback to default email
            'payer_phone' => $data['payer_phone'] ?? '+78888884545', // Fallback to default phone number
            'metadata' => $data['metadata'] ?? '', // Fallback to empty string
            'expires_at' => $data['expires_at'] ?? now()->addHours(24), // Use $data['expires_at'] or fallback to current time
            'processed_at' => $data['processed_at'] ?? time(), // Use $data['processed_at'] or fallback to current time
        ]);

        $payment->save();

        $merchantCoupon = MerchantCoupons::create([
            'merchant_id' => $data['merchant_id'] ?? null, // Fallback to null if not provided
            'payment_id' => $payment->id, // Payment ID should always be set
            'code' => $couponCode, // Coupon code is passed directly
            'amount' => $data['amount'] ?? 0, // Fallback to 0 if 'amount' is not provided
            'currency_id' => $data['currency_id'] ?? 2, // Fallback to 2 (USD) if not provided
            'status' => $data['status'] ?? MerchantCoupons::STATUS_PENDING_STRING, // Use $data['status'] or fallback to pending
            'operator_id' => $data['operator_id'] ?? null, // Fallback to null if not provided
            'expires_at' => $data['expires_at'] ?? now()->addHours(24), // Use $data['expires_at'] or fallback to current time
            'verified_at' => $data['verified_at'] ?? null, // Fallback to null if not provided
            'used_at' => $data['used_at'] ?? null, // Fallback to null if not provided
        ]);

        $merchantCoupon->save();

        // Сумму к оплате
        // Код купона
        // Инструкцию где и как можно оплатить
        // Срок действия купона (например, 24 часа)
        $response = [
            'success' => true,
            'Message' => 'created',
            'status' => 200,
            'data' => [
                'amount' => $merchantCoupon->amount,
                'code' => $merchantCoupon->code,
                'terms_of_use' => 'Coupon is valid for one-time use only, non-transferable, and expires 24 hours after issuance. Cannot be combined with other offers. See full terms for more details.',
                'expires_at' => $merchantCoupon->expires_at,
            ]
        ];
        return  Response::json($response, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(MerchantCoupons $merchantCoupons)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MerchantCoupons $merchantCoupons)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MerchantCoupons $merchantCoupons)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MerchantCoupons $merchantCoupons)
    {
        //
    }

    /**
     * Coupon use page
     */
    public function use(Request $request)
    {
        return Inertia::render('TestMerchantCouponUse', [
            'title' => 'Главная | ' . config('app.name'),
            'merchant_id' => $request->query('merchant_id'),
        ]);
    }


    // Additional custom action
    public function verify($id)
    {
        $coupon = MerchantCoupons::findOrFail($id);
        // Logic for verification (for example)
        $coupon->status = MerchantCoupons::STATUS_VERIFIED_STRING;
        $coupon->save();

        return response()->json(['message' => 'Coupon verified successfully']);
    }

    // Another custom action
    public function expire($id)
    {
        $coupon = MerchantCoupons::findOrFail($id);
        // Logic for expiration
        $coupon->status = MerchantCoupons::STATUS_EXPIRED_STRING;
        $coupon->expires_at = now(); // Set expiration time
        $coupon->save();

        return response()->json(['message' => 'Coupon expired']);
    }
}
