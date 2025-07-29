<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'membership_type',
        'referral_code',
        'commission_rate',
        'membership_upgraded_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'membership_upgraded_at' => 'datetime',
        'commission_rate' => 'decimal:2',
        'password' => 'hashed',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                $user->referral_code = $user->generateUniqueReferralCode();
            }
        });
    }

    /**
     * Generate a unique referral code.
     */
    public function generateUniqueReferralCode(): string
    {
        do {
            $code = 'REF' . strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Check if user is VIP member.
     */
    public function isVip(): bool
    {
        return in_array($this->membership_type, ['gold', 'platinum', 'diamond']);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->membership_type === 'admin';
    }

    /**
     * Check if user can refer for diamond tier.
     */
    public function canReferDiamond(): bool
    {
        return $this->isVip();
    }

    /**
     * Get commission rate for specific tier.
     */
    public function getCommissionRateForTier(string $tier): float
    {
        $rates = config('paymongo.commission_rates');
        
        if ($this->isVip()) {
            return $rates['vip'][$tier] ?? 0.0;
        }
        
        return $rates['free'][$tier] ?? 0.0;
    }

    /**
     * Update referral code when upgrading to VIP.
     */
    public function upgradeToVip(string $tier): void
    {
        $this->update([
            'membership_type' => $tier,
            'referral_code' => 'VIP' . strtoupper(Str::random(8)),
            'commission_rate' => $this->getCommissionRateForTier($tier),
            'membership_upgraded_at' => now(),
        ]);
    }

    /**
     * Get memberships relationship.
     */
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Get active membership.
     */
    public function activeMembership()
    {
        return $this->hasOne(Membership::class)
                    ->where('payment_status', 'completed')
                    ->latest();
    }

    /**
     * Get referrals sent by this user.
     */
    public function referralsSent()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get referrals received by this user.
     */
    public function referralsReceived()
    {
        return $this->hasMany(Referral::class, 'referred_id');
    }

    /**
     * Get transactions.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get total commission earned.
     */
    public function getTotalCommissionAttribute(): float
    {
        return $this->referralsSent()
                    ->where('status', 'paid')
                    ->sum('commission_amount');
    }

    /**
     * Get pending commission.
     */
    public function getPendingCommissionAttribute(): float
    {
        return $this->referralsSent()
                    ->whereIn('status', ['pending', 'approved'])
                    ->sum('commission_amount');
    }

    /**
     * Get membership tier display name.
     */
    public function getMembershipDisplayAttribute(): string
    {
        return match($this->membership_type) {
            'free' => 'Free Member',
            'gold' => 'Gold VIP',
            'platinum' => 'Platinum VIP',
            'diamond' => 'Diamond VIP',
            'admin' => 'Administrator',
            default => 'Unknown'
        };
    }
}
