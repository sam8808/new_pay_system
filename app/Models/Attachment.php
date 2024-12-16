<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketMessage;

class Attachment extends Model
{
    use HasFactory;
    CONST UPDATED_AT = null;
    protected $table = 'ticket_attachments';
    protected $fillable = [
        'ticket_message_id',
        'filename',
        'filepath',
        'filesize',
        'mimetype'
    ];

    public function message(){
        return $this->belongsTo(TicketMessage::class, 'message_id');
    }
}
