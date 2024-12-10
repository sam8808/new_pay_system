<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayPayment extends Model
{
    use HasFactory;

    protected $table = 'gateway_payment'; 

    // Fillable fields
    protected $fillable = [
        'merchant_id',
        'order_id',
        'amount',
        'currency',
        'status',
    ];

    // created, processing, completed, failed, refunded
    const STATUS_CREATED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;
    const STATUS_REFUNDED = 4;

    public function webhookNotifications()
    {
        return $this->hasMany(WebhookNotification::class);
    }
}
