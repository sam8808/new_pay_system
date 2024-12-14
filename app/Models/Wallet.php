<?php

namespace App\Models;

use App\Models\User;
use App\Models\Currency;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'balance',
        'reserved_balance',
        'is_active',
        'is_show',
        'is_default',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'reserved_balance' => 'decimal:8',
        'is_active' => 'boolean',
        'is_show' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
}
