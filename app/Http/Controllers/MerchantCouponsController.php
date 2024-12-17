<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMerchantCouponsRequest;
use App\Http\Requests\UpdateMerchantCouponsRequest;
use App\Models\MerchantCoupons;
use App\Models\Payment;
use App\Models\Merchant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use App\Http\Controllers\API\GatewayController;

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
        $couponCode = substr(Str::uuid()->toString(), 0, 8);
        $merchant_id = Merchant::where('merchant_id', $data['merchant_id'])->first()->id;

        $payment = Payment::create([
            'uuid' => $uuid,
            'merchant_id' => $merchant_id,
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
            'merchant_id' => $merchant_id,
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

    /**
     * Verify a coupon and process its payment.
     *
     * This method performs the following:
     * - Finds a pending coupon by its code.
     * - Validates if the coupon exists, is not expired, and has a valid amount.
     * - Processes the coupon payment through the GatewayController.
     * - Marks the coupon as used, confirms the payment, and updates the user's wallet balance.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request containing the coupon code.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the verification result.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the coupon does not exist or has already been used.
     */

    public function verify(Request $request)
    {
        // Find the coupon by code and ensure it is pending
        $coupon = MerchantCoupons::where('code', $request->code)
            ->where('status', MerchantCoupons::STATUS_PENDING_STRING)
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Coupon not found or already used'], 404);
        }

        // Check expiration date
        if ($coupon->expires_at && $coupon->expires_at < now()) {
            return response()->json(['message' => 'Coupon has expired'], 400);
        }

        // Check validity of amount
        if ($coupon->amount <= 0) {
            return response()->json(['message' => 'Invalid coupon amount'], 400);
        }

        if((new GatewayController())->processCouponPayment($coupon)){
            // Mark coupon as used
            $coupon->status = MerchantCoupons::STATUS_USED_STRING;
            $coupon->used_at = now();
            $coupon->save();

            // Confirm payment (example: marking payment as successful)
            $coupon->payment->status = Payment::STATUS_COMPLETED_STRING;
            $coupon->payment->save();

            // update user balance
            $wallet = $coupon->payment->merchant->user->wallets()->where('currency_id', $coupon->payment->currency_id)->first();
            $wallet->addToBalance($coupon->payment->amount_in_base_currency);

            return response()->json([
                'message' => 'Coupon verified and payment confirmed',
                'code' => $coupon->code,
                'amount' => $coupon->amount,
                'success' => true
            ], 200);
        }else{
            return response()->json([
                'message' => 'Coupon not verified and payment not confirmed',
                'code' => $coupon->code,
                'amount' => $coupon->amount,
                'success' => false
            ], 400);
        }
    }
}
