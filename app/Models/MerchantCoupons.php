<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantCoupons extends Model
{
    use HasFactory;

    // Define constants for status values
    const STATUS_PENDING_STRING = 'pending';
    const STATUS_VERIFIED_STRING = 'verified';
    const STATUS_USED_STRING = 'used';
    const STATUS_EXPIRED_STRING = 'expired';
    const STATUS_CANCELED_STRING = 'canceled';

    protected $fillable = [
        'merchant_id',
        'payment_id',
        'code',
        'amount',
        'currency_id',
        'status',
        'operator_id',
        'expires_at',
        'verified_at',
        'used_at',
    ];
}
