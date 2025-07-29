<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'membership_tier_referred',
        'commission_amount',
        'commission_rate',
        'status',
        'approved_at',
        'paid_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user who made the referral.
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred.
     */
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Scope for pending referrals.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved referrals.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid referrals.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Get tier display name.
     */
    public function getTierDisplayAttribute(): string
    {
        if (!$this->membership_tier_referred) {
            return 'Registration Only';
        }

        return match($this->membership_tier_referred) {
            'gold' => 'Gold VIP',
            'platinum' => 'Platinum VIP',
            'diamond' => 'Diamond VIP',
            default => 'Unknown Tier'
        };
    }

    /**
     * Get formatted commission amount.
     */
    public function getFormattedCommissionAttribute(): string
    {
        return '₱' . number_format($this->commission_amount, 2);
    }

    /**
     * Get status display.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => 'Unknown Status'
        };
    }

    /**
     * Calculate commission based on referrer type and tier.
     */
    public static function calculateCommission(User $referrer, string $tier, float $amount): array
    {
        $commissionRate = $referrer->getCommissionRateForTier($tier);
        $commissionAmount = ($amount * $commissionRate) / 100;

        return [
            'rate' => $commissionRate,
            'amount' => $commissionAmount,
        ];
    }

    /**
     * Create referral record.
     */
    public static function createReferral(User $referrer, User $referred, ?string $tier = null, ?float $amount = null): self
    {
        $commission = ['rate' => 0, 'amount' => 0];
        
        if ($tier && $amount) {
            // Check if referrer can refer this tier
            if ($tier === 'diamond' && !$referrer->canReferDiamond()) {
                throw new \Exception('Free users cannot refer Diamond tier memberships.');
            }
            
            $commission = self::calculateCommission($referrer, $tier, $amount);
        }

        return self::create([
            'referrer_id' => $referrer->id,
            'referred_id' => $referred->id,
            'membership_tier_referred' => $tier,
            'commission_rate' => $commission['rate'],
            'commission_amount' => $commission['amount'],
            'status' => 'pending',
        ]);
    }

    /**
     * Approve referral.
     */
    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Cancel referral.
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);
    }
}
