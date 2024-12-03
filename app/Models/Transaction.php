<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'wallet_id',
        'currency_id',
        'transactionable_type',
        'transactionable_id',
        'amount',
        'fee',
        'amount_in_base_currency',
        'type', // deposit, payment, withdrawal, transfer, exchange
        'status',
        'metadata'
    ];



    protected $casts = [
        'amount' => 'decimal:8',
        'fee' => 'decimal:8',
        'amount_in_base_currency' => 'decimal:8',
        'metadata' => 'array',
    ];


    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
