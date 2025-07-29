<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tier',
        'amount',
        'payment_status',
        'transaction_id',
        'paymongo_payment_id',
        'payment_details',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the membership.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for completed memberships.
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope for pending memberships.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Get tier display name.
     */
    public function getTierDisplayAttribute(): string
    {
        return match($this->tier) {
            'gold' => 'Gold VIP',
            'platinum' => 'Platinum VIP',
            'diamond' => 'Diamond VIP',
            default => 'Unknown Tier'
        };
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Check if membership is active.
     */
    public function isActive(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Mark membership as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'payment_status' => 'completed',
            'paid_at' => now(),
        ]);

        // Upgrade user membership
        $this->user->upgradeToVip($this->tier);
    }

    /**
     * Mark membership as failed.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }
}
