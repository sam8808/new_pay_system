<?php

namespace App\Models;

use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\PaymentSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'merchant_id',
        'payment_system_id',
        'currency_id',
        'order_id', // ID заказа в системе мерчанта
        'amount',
        'processing_fee',
        'amount_in_base_currency',
        'status',
        'external_id',
        'payer_email',
        'payer_phone',
        'metadata',
        'expires_at',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'processing_fee' => 'decimal:8',
        'amount_in_base_currency' => 'decimal:8',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'processed_at' => 'datetime',
    ];


    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }


    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
