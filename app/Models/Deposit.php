<?php

namespace App\Models;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\PaymentSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
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
        'payer_email',
        'payer_phone',
        'metadata',
        'status',
        'expires_at',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'processing_fee' => 'float',
        'amount_in_base_currency' => 'float',
        'metadata' => 'json',
        'expires_at' => 'datetime',
        'processed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function paymentSystem(): BelongsTo
    {
        return $this->belongsTo(PaymentSystem::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
