<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'supports_tickets';
    protected $fillable = [
        'uuid',
        'user_id',
        'subject',
        'priority',
        'status',
        'category',
        'assigned_to',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assignedTo()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');

    }
}
