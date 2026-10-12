<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'username',
        'ref_by',
        'balance',
        'plan_id',
        'expire_date',
        'status',
        'kv',
        'ev',
        'sv',
        'profile_complete',
        'ver_code',
        'ver_code_send_at',
        'ts',
        'tv',
        'tsc',
        'ban_reason',
        'remember_token',
        'address',
        'kyc_data',
        'daily_limit',
        'pv',
        'received_first_click_bonus',
        'level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'signup_password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime',
        'received_first_click_bonus' => 'boolean'
    ];

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }
    
    public function deposit()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }

    public static function composeDisplayName(?string $first, ?string $last, ?string $fallback = null): string
    {
        $first = trim((string) $first);
        $last  = trim((string) $last);

        if ($last === '' || mb_strtolower($first) === mb_strtolower($last)) {
            return $first !== '' ? $first : (string) ($fallback ?? '');
        }

        if ($first !== '' && preg_match('/\s' . preg_quote($last, '/') . '$/iu', $first)) {
            return $first;
        }

        return trim($first . ' ' . $last);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => self::composeDisplayName(
                $this->first_name ?? $this->firstname ?? '',
                $this->last_name ?? $this->lastname ?? '',
                $this->username ?? 'User'
            ),
        );
    }

    public function runningPlan(): Attribute
    {
        if ($this->plan && $this->expire_date > now()) {
            $running = true;
        }else{
            $running = false;
        }
        return new Attribute(
            get: fn () => $running,
        );
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function clicks()
    {
        return $this->hasMany(PtcView::class);
    }

    public function commissions()
    {
        return $this->hasMany(CommissionLog::class);
    }

    public function refBy()
{
    return $this->belongsTo(User::class,'ref_by');
}

public function referredUsers()
{
    return $this->hasMany(User::class, 'ref_by');
}

public static function generateReferralCode()
{
    $length = 8;
    do {
        $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $length));
    } while (self::where('username', $code)->exists());
    
    return $code;
}

    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', 0);
    }

    public function scopeKycUnverified()
    {
        return $this->where('kv', 0);
    }

    public function scopeKycPending()
    {
        return $this->where('kv', 2);
    }

    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', 1);
    }

    public function scopeWithBalance()
    {
        return $this->where('balance','>', 0);
    }
}