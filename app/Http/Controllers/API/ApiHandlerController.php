<?php

namespace App\Http\Controllers\API;

use App\Models\Merchant;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ApiHandlerController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|string
     */
    public function __invoke(Request $request): JsonResponse|string
    {
        $request->session()->put('shop', $request->post('shop'));
        $merchant = Merchant::approvedAndActivated()
            ->where('m_id', $request->post('shop'))
            ->firstOrFail();

        $service = new ApiService($request, $merchant);

        $service->validate();

        if (!$service->merchantExists()) {
            return response()->json(['message' => 'Merchant not found'], 422);
        }

        if (!$service->verifyHash()) {
            return response()->json(['message' => 'Hash no verified'], 422);
        }

        return redirect()->route('api.index', $request->all());
    }
}
