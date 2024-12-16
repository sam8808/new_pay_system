<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model
{
    use HasFactory;

        protected $fillable = [
            'user_id',
            'code' ,
            'type' ,
            'expires_at'
        ];
}
