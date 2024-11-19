<?php

namespace App\Models;

use App\Models\PaymentSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentSystemDetail extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'ps_id',
        'title',
        'value',
        'activated',
    ];


    /**
     * @var string[]
     */
    protected $casts = [
        'activated' => 'boolean',
    ];


    public function paymentSystem(): BelongsTo
    {
        return $this->belongsTo(PaymentSystem::class, 'ps_id');
    }
}
