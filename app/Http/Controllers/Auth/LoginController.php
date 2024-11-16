<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Auth\LoginService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $service = LoginService::init($request->all());

        if (!$service->validate()) {
            return Redirect::back()->withErrors($service->getErrors());
        }

        $service->store();

        if ($service->fail()) {
            return Redirect::back()->withErrors($service->getErrors());
        }

        return $service->responseSuccess();
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('home');
    }
}
