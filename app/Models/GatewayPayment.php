<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayPayment extends Model
{
    use HasFactory;

    public function webhookNotifications()
    {
        return $this->hasMany(WebhookNotification::class);
    }
}
