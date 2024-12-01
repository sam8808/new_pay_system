<?php

namespace App\Models;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Currency;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'wallet_id',
        'currency_id',
        'payment_id',
        'withdrawal_id',
        'amount',
        'fee',
        'amount_in_base_currency',
        'type',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fee' => 'decimal:8',
        'amount_in_base_currency' => 'decimal:8',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }
}
