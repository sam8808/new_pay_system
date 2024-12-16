<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function upload(Request $request){
        $validated = $request->validate([
            'file' => 'required|file|max:2048',
            'message_id' => 'required|exists:message_id,id',
        ]);

        if($request->hasFile('file')){
            $file = $request->file('file');
            $path = $file->store('public/attachments');

            $attachment = Attachment::create([
                'ticket_message_id' => $validated['message_id'],
                'filename' => $file->getClientOriginalName(),
                'filepath' => $path,
                'filesize' => $file->getSize(),
                'mimetype' => $file->getClientMimeType(),
            ]);

            return response()->json([
                'message' => 'file uploaded successfully',
                'attachment' => $attachment,

            ], 201);
        }
        return response()->json([
            'message' => 'file not uploaded',
        ], 400);
    }
}
