<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Auth\RegisterService;

class RegisterController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Register');
    }


    public function store(Request $request)
    {
        $service = RegisterService::init($request->all());

        if (!$service->validate()) {
            return response()->json(['error' => $service->getErrors()], 422);
        }

        $service->store();

        if ($service->fail()) {
            return response()->json(['error' => $service->getErrors()], 422);
        }

        // $service->welcomeMailNotify();
        // $bot = Telegram::init();

        // $message = __('Register new user') . PHP_EOL .
        //     __('Email') . ': ' . $service->user()->email;

        // $bot->sendMessage($message);
        return $service->responseSuccess();
    }
}
