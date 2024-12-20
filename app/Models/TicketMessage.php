<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;

class TicketMessage extends Model
{
    use HasFactory;
    protected $table = 'ticket_messages';
    protected $fillable = [
        'ticket_id',
        'sender_type',
        'sender_id',
        'message',
        'attachments',
        'is_internal'
    ];

}
