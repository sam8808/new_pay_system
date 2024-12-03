<?php

namespace App\Http\Controllers\Account;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\ProfileService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Account/Profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $service = ProfileService::init($request->all(), $request->user());

        if (!$service->updateProfile()) {
            return redirect()->back()->withErrors($service->getErrors());
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateContacts(Request $request): RedirectResponse
    {
        $service = ProfileService::init($request->all(), $request->user());

        if (!$service->updateContacts()) {
            return redirect()->back()->withErrors($service->getErrors());
        }

        return redirect()->route('profile.edit')->with('status', 'contact-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $service = ProfileService::init($request->all(), $request->user());

        if (!$service->deleteAccount()) {
            return redirect()->back()->withErrors($service->getErrors());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}
