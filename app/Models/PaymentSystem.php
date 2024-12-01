<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Currency;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'provider',
        'provider_settings',
        'logo',
        'currency_id',
        'type',
        'min_amount',
        'max_amount',
        'processing_fee',
        'processing_time',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'provider_settings' => 'array',
        'min_amount' => 'decimal:8',
        'max_amount' => 'decimal:8',
        'processing_fee' => 'decimal:4',
        'processing_time' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
}

