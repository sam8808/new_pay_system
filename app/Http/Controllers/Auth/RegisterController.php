<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailNotification;
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

        if (!$service->validate()) {
            return Redirect::back()->withErrors($service->getErrors());
        }

        $service->store();

        if ($service->fail()) {
            return Redirect::back()->withErrors($service->getErrors());
        }

        $message = "🔔 Новый пользователь!\n" .
            "Email: {$service->user()->email}\n" .
            "Username: {$service->user()->username}";

        SendEmailNotification::dispatch($service->user());
        SendTelegramNotification::dispatch($message);

        return  Redirect::back()->with([
            'message' => __('Registration successful'),
            'status' => 'success',
        ]);
    }
}
