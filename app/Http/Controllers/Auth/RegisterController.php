<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Jobs\CreateUserWallets;
use App\Jobs\SendEmailNotification;
use App\Services\Auth\LoginService;
use App\Http\Controllers\Controller;
use App\Jobs\SendTelegramNotification;
use App\Services\Auth\RegisterService;
use Illuminate\Support\Facades\Redirect;

class RegisterController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Register');
    }


    public function store(Request $request)
    {
        $service = RegisterService::init($request->all());
        $loginService = LoginService::init($request->only(['email', 'password']));

        if (!$service->validate()) {
            return Redirect::back()->withErrors($service->getErrors());
        }

        $service->store();

        if ($service->fail()) {
            return Redirect::back()->withErrors($service->getErrors());
        }


        $message = "🔔 Новый пользователь!\n" .
            "Email: {$service->user()->email}\n" .
            "ID: {$service->user()->account}";

        CreateUserWallets::dispatch($service->user());
        SendEmailNotification::dispatch($service->user());
        SendTelegramNotification::dispatch($message);

        $loginService->store();

        return  Redirect::route('dashboard');
    }
}
