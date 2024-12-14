<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{

    public function index()
    {
        return Inertia::render('Admin/Login');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        if (auth()->guard('admin')->attempt($credentials)) {
            return redirect()->route('admin');
        }

        return back()->withErrors(['error' => __('Неправильный Логин или пароль')]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        auth()->guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
