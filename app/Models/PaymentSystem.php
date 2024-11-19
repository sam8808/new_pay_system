<?php

namespace App\Models;

use App\Models\PaymentSystemDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentSystem extends Model
{
    use HasFactory;


    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'desc',
        'url',
        'logo',
        'currency',
        'activated',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'activated' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(PaymentSystemDetail::class, 'ps_id');
    }

    /**
     * @return HasMany
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'payment_system');
    }
}
