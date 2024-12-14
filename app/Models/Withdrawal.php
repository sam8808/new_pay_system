<?php

namespace App\Models;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\PaymentSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'external_id',
        'user_id',
        'wallet_id',
        'payment_system_id',
        'currency_id',
        'amount',
        'processing_fee',
        'amount_in_base_currency',
        'recipient_data',
        'metadata',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'processing_fee' => 'decimal:8',
        'amount_in_base_currency' => 'decimal:8',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function paymentSystem()
    {
        return $this->belongsTo(PaymentSystem::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
