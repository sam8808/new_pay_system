<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'symbol',
        'type',
        'icon',
        'is_active',
        'is_base',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'is_base' => 'boolean'

    ];

    

    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }
}
