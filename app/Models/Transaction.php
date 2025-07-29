<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'currency',
        'payment_status',
        'external_payment_id',
        'payment_method',
        'payment_metadata',
        'failure_reason',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope for failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    /**
     * Scope for membership upgrade transactions.
     */
    public function scopeMembershipUpgrade($query)
    {
        return $query->where('type', 'membership_upgrade');
    }

    /**
     * Scope for commission payout transactions.
     */
    public function scopeCommissionPayout($query)
    {
        return $query->where('type', 'commission_payout');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'membership_upgrade' => 'Membership Upgrade',
            'commission_payout' => 'Commission Payout',
            default => 'Unknown Transaction'
        };
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => 'Unknown Status'
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Mark transaction as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'payment_status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'failure_reason' => $reason,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark transaction as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'payment_status' => 'processing',
        ]);
    }

    /**
     * Create membership upgrade transaction.
     */
    public static function createMembershipUpgrade(
        User $user,
        string $tier,
        float $amount,
        string $paymentMethod = null
    ): self {
        return self::create([
            'user_id' => $user->id,
            'type' => 'membership_upgrade',
            'amount' => $amount,
            'currency' => 'PHP',
            'payment_method' => $paymentMethod,
            'payment_metadata' => [
                'tier' => $tier,
                'user_membership_before' => $user->membership_type,
            ],
        ]);
    }

    /**
     * Create commission payout transaction.
     */
    public static function createCommissionPayout(
        User $user,
        float $amount,
        array $referralIds = []
    ): self {
        return self::create([
            'user_id' => $user->id,
            'type' => 'commission_payout',
            'amount' => $amount,
            'currency' => 'PHP',
            'payment_metadata' => [
                'referral_ids' => $referralIds,
                'payout_date' => now()->toDateString(),
            ],
        ]);
    }
}
