<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorService;
use App\Http\Requests\Auth\TwoFactorRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService
    ) {}

    // Включение/выключение 2FA
    public function toggle(Request $request): JsonResponse
    {
        $user = $request->user();
        $enabled = $this->twoFactorService->toggle(
            $user,
            $request->input('type')
        );

        return response()->json([
            'message' => $enabled
                ? 'Two-factor authentication enabled'
                : 'Two-factor authentication disabled'
        ]);
    }

    // Проверка кода 2FA
    public function verify(TwoFactorRequest $request): JsonResponse
    {
        $verified = $this->twoFactorService->verify(
            $user = $this->getUser($request),
            $request->input('code')
        );

        return response()->json([
            'verified' => $verified
        ]);
    }

    protected function getUser($request){
        // Find the user by email
        $user = User::where('email', $request->form['email'])->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'sent' => false
            ]);
        }

        return $user;
    }

    // Отправка кода 2FA
    public function send(Request $request)
    {
        $user = $this->getUser($request);

        // Send the code via your service and return response
        $sent = $this->twoFactorService->sendCode($user);

        return response()->json([
            'sent' => $sent,
        ]);
    }

}
