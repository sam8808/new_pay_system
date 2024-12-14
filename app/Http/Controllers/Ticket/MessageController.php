<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\TicketMessage;
class MessageController extends Controller
{
    public function index(){
        return response()->json(['hi']);
    }

    public function createMessage(Request $request){
            $message = TicketMessage::create(
                array_merge(
                    $request->all(),
                    [
                        'sender_id' => auth()->id(),
                        'is_internal' => false
                    ]
                )
            );

            return response()->json([
                'message' => 'Message sent successfully!',
                'messageObject' => $message
            ], 201);
    }

}
