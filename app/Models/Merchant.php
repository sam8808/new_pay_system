<?php

namespace App\Models;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'domain',
        'description',
        'merchant_id',
        'api_key',
        'secret_key',
        'webhook_url',
        'type',
        'processing_fee',
        'allowed_ips',
        'is_active',
        'is_succes_moderation',
    ];

    protected $casts = [
        'allowed_ips' => 'array',
        'processing_fee' => 'decimal:4',
        'is_active' => 'boolean',
        'is_succes_moderation' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
