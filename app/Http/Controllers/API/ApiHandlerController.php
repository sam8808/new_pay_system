<?php

namespace App\Http\Controllers\API;

use App\Models\Merchant;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\SignatureService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiHandlerController extends Controller
{
    public function __invoke(Request $request): JsonResponse|string
    {
        try {
            if (!$shop = $request->post('shop')) {
                return $this->error('Shop parameter is required', 422);
            }

            $request->session()->put('shop', $shop);

            $merchant = Merchant::approvedAndActivated()
                ->where('m_id', $shop)
                ->firstOrFail();

            $signatureService = new SignatureService();

            $service = new ApiService($request, $merchant, $signatureService);


            try {
                $service->validate();
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::warning('Validation failed', [
                    'shop' => $shop,
                    'errors' => $e->errors()
                ]);
                return $this->error($e->getMessage(), 422);
            }

            if (!$service->merchantExists()) {
                Log::warning('Merchant validation failed', ['shop' => $shop]);
                return $this->error('Merchant not found', 422);
            }

            if (!$service->verifyHash()) {
                Log::warning('Hash verification failed', [
                    'shop' => $shop,
                    'provided_hash' => $request->post('signature')
                ]);
                return $this->error('Hash verification failed', 422);
            }

            $params = array_merge($request->all(), ['timestamp' => now()->timestamp]);
            return redirect()->route('api.index', $params);
        } catch (ModelNotFoundException $e) {
            Log::warning('Merchant not found', ['shop' => $shop ?? null]);
            return $this->error('Merchant not found', 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error', [
                'shop' => $shop ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->error('An unexpected error occurred', 500);
        }
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'timestamp' => now()->timestamp
        ], $status);
    }
}
