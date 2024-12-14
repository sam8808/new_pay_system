<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Ticket;
class HomeController extends Controller
{
    public function index()
    {
        $ticket = null ;
        if(auth()->id() !== null){
            $ticket = Ticket::where('user_id', auth()->id())->get();
        }else{
            $ticket = Ticket::where('status', 'waiting_for_customer')->with('user')->get();
        }
        return Inertia::render('Home', [
            'title' => 'Главная | ' . config('app.name'),
            'ticket' => $ticket,
            'auth' => !(auth()->check()),
        ]);
    }
}
