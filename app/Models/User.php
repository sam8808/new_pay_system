<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cookie;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'account',
        'email',
        'password',
        'telegram',
        'phone',
        'referrer_id',
        'is_active',
        'is_verified',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referrer_id');
    }
    public function getReferralsCount(){
        return $this->referrals()->count();
    }
    public function referralStats(){
        return [
            'referralCode'  => $this->ref_code,
            'totalEarnings' => 15000,
            'activeReferrals' => $this->getReferralsCount(),
            'totalReferrals' => $this->getReferralsCount(),
            'conversionRate' => 48,
            'referralLink' => url("/invite/{$this->ref_code}"),
        ];

    }
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function merchants()
    {
        return $this->hasMany(Merchant::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
    static function getReferer(){
        return Cookie::get('ref_code');
    }
}
