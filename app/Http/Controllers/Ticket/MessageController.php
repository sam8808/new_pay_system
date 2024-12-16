<?php

namespace App\Http\Controllers\Ticket;

use App\Events\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\TicketMessage;
class MessageController extends Controller
{
    public function index(Request $request){
        print_r(auth()->guard('admin')->id());
        return ;
    }

    public function createMessage(Request $request){
        try{
            $sender_type = auth()->guard('admin')->check() ? 'agent' : 'user';
            if($message = (trim($request->get('message')) === '') && $request->hasFile('files')){
                $message = ' ';
            }
            $request->validate(['files.*' => 'nullable|file|max:2048']);
            $message = TicketMessage::create(
                array_merge(
                    $request->all(),
                    [
                        'message' => $message,
                        'sender_type' => $sender_type,
                        'sender_id' => auth()->id(),
                        'is_internal' => false
                    ]
                )
            );
            $attachments = [];
            if($request->hasFile('files')){
                foreach ($request->file('files') as $file) {
                    $path = $file->store('public/messages');
                    $attachment = Attachment::create([
                        'ticket_message_id' => $message['id'],
                        'filename' => $file->getFilename(),
                        'filepath' => $path,
                        'filesize' => $file->getSize(),
                        'mimetype' => $file->getMimeType(),
                    ]);
                    $attachments[] = $attachment['id'];
                }
                $message->update([
                    'attachments' => $attachments
                ]);
            }

            return response()->json([
                'message' => 'Message sent successfully!',
                'messageObject' => $message
            ], 201);
        }catch (ValidationException $e){

            return response()->json(['message' => $e->getMessage()], 400);
        }

    }
    public function sendMessage(Request $request){
        broadcast(new SendMessage($request->get('message')) );
        return response('ok');
    }



}
