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

    function __construnct(){
    }

    public function index()
    {
        $data = [];
        if($id = $this->isAdmin()){
            $data['mine'] = Ticket::where("assigned_to", $id)->with('user')->get();
            $data['waiting'] = Ticket::where(["assigned_to" => null, 'status' => 'waiting_for_customer'])->with('user')->get();
        }else{
            $data = Ticket::where('user_id', Auth::id())->with(['user:id,email', 'assignedTo:id,username'])->get();
        }

        return $data;
    }
    private function isAdmin(){
        return auth()->guard('admin')->id();
    }
    public function createTicket(Request $request){
        try{
            $validated = $request->validate([
                'category' => 'required|in:technical,financial,account,wallet,other',
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
    public function assign(Request $request){
        try{
            $id = $request->validate([
                'id' => 'required'
            ]);

            $id = $id['id'];

            Ticket::where('id', $id)->update([
                'status' => 'in_progress',
                'assigned_to' => auth()->guard('admin')->id(),
            ]);
            return response()->json(['message' => 'ticket updated successfully'], 200);
        }catch(ValidationException $e){
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 400);
        }
    }
    public function update(Request $request){
        try{
            $validate = $request->validate([
                'id' => 'required',
            ]);
            Ticket::where("id", $validate['id'])->update($validate);
        }catch (ValidationException $e){
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 400);
        }
    }
}
