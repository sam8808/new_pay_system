<?php

namespace App\Http\Controllers;

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
        ]);
    }
}
