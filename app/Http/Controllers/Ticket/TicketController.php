<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index()
    {
        return response()->json([]);
    }
    public function createTicket(Request $request){
        try{
            $validated = $request->validate([
                'category' => 'required|in:technical,financial,account',
                'subject' => 'required|max:255'
            ]);
            $ticket = Ticket::Create(
                array_merge(
                    $validated,
                    [
                        'user_id' => Auth::id(),
                        'uuid' => Str::uuid(),
                        'status' => 'waiting_for_customer'
                    ]
                )
            );
            return response()->json(['message' => 'created successfully', 'ticket' => $ticket], 201);
        }catch (ValidationException $e){
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 400);
        }catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }
    public function getAllMessages($id){
        return TicketMessage::where("ticket_id", $id)->get();
    }
}
