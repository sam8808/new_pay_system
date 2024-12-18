<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;
use App\Models\Ticket;
class HomeController extends Controller
{
    public function index()
    {
        $ticket = null ;
        return Inertia::render('Home', [
            'title' => 'Главная | ' . config('app.name'),
            'auth' => auth()->check(),
            'admin' => (auth()->guard('admin')->check()),
            'referrer' => User::getReferer(),
        ]);
    }
    public function invite(Request $request, $ref_code){
        $referrer = User::where('ref_code', $ref_code)->first();
        if($referrer){
            Cookie::queue('ref_code', $ref_code, 60 * 24 * 30);
            return redirect()->route('home')->with('success', 'Рефералный код действителен');
        }
        return redirect()->route('home')->with('error', 'Рефералный код не действителен') ;
    }
}
