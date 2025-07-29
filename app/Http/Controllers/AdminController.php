<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Membership;
use App\Models\Referral;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'free_users' => User::where('membership_type', 'free')->count(),
            'vip_users' => User::whereIn('membership_type', ['gold', 'platinum', 'diamond'])->count(),
            'gold_members' => User::where('membership_type', 'gold')->count(),
            'platinum_members' => User::where('membership_type', 'platinum')->count(),
            'diamond_members' => User::where('membership_type', 'diamond')->count(),
            'total_revenue' => Transaction::where('payment_status', 'completed')
                                        ->where('type', 'membership_upgrade')
                                        ->sum('amount'),
            'pending_payments' => Transaction::where('payment_status', 'pending')->count(),
            'total_referrals' => Referral::count(),
            'pending_commissions' => Referral::whereIn('status', ['pending', 'approved'])->sum('commission_amount'),
            'paid_commissions' => Referral::where('status', 'paid')->sum('commission_amount'),
        ];

        // Recent activities
        $recentUsers = User::latest()->take(5)->get();
        $recentMemberships = Membership::with('user')->latest()->take(5)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(5)->get();

        // Monthly revenue chart data
        $monthlyRevenue = Transaction::where('payment_status', 'completed')
                                   ->where('type', 'membership_upgrade')
                                   ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
                                   ->groupBy('year', 'month')
                                   ->orderBy('year', 'desc')
                                   ->orderBy('month', 'desc')
                                   ->take(12)
                                   ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentUsers', 
            'recentMemberships', 
            'recentTransactions',
            'monthlyRevenue'
        ));
    }

    /**
     * Display users management page.
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by membership type
        if ($request->filled('membership_type')) {
            $query->where('membership_type', $request->membership_type);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20);

        $membershipTypes = [
            'free' => 'Free Members',
            'gold' => 'Gold VIP',
            'platinum' => 'Platinum VIP',
            'diamond' => 'Diamond VIP',
            'admin' => 'Administrators',
        ];

        return view('admin.users', compact('users', 'membershipTypes'));
    }

    /**
     * Show specific user details.
     */
    public function showUser(User $user)
    {
        $user->load(['referralsSent', 'referralsReceived', 'memberships', 'transactions']);

        $stats = [
            'total_referrals' => $user->referralsSent()->count(),
            'total_commission' => $user->total_commission,
            'pending_commission' => $user->pending_commission,
            'total_spent' => $user->transactions()
                                 ->where('payment_status', 'completed')
                                 ->sum('amount'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Update user membership.
     */
    public function updateUserMembership(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'membership_type' => 'required|in:free,gold,platinum,diamond,admin',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldMembership = $user->membership_type;
        
        if ($request->membership_type !== 'free' && $oldMembership === 'free') {
            $user->upgradeToVip($request->membership_type);
        } else {
            $user->update([
                'membership_type' => $request->membership_type,
                'commission_rate' => $user->getCommissionRateForTier($request->membership_type),
            ]);
        }

        return back()->with('success', "User membership updated from {$oldMembership} to {$request->membership_type}.");
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin users.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Display memberships management page.
     */
    public function memberships(Request $request)
    {
        $query = Membership::with('user');

        // Filter by tier
        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $memberships = $query->latest()->paginate(20);

        $tiers = ['gold' => 'Gold', 'platinum' => 'Platinum', 'diamond' => 'Diamond'];
        $statuses = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled'
        ];

        return view('admin.memberships', compact('memberships', 'tiers', 'statuses'));
    }

    /**
     * Update membership status.
     */
    public function updateMembershipStatus(Request $request, Membership $membership)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldStatus = $membership->payment_status;

        if ($request->status === 'completed' && $oldStatus !== 'completed') {
            $membership->markAsCompleted();
        } elseif ($request->status === 'failed') {
            $membership->markAsFailed();
        } else {
            $membership->update(['payment_status' => $request->status]);
        }

        return back()->with('success', "Membership status updated from {$oldStatus} to {$request->status}.");
    }

    /**
     * Display referrals management page.
     */
    public function referrals(Request $request)
    {
        $query = Referral::with(['referrer', 'referred']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tier
        if ($request->filled('tier')) {
            $query->where('membership_tier_referred', $request->tier);
        }

        $referrals = $query->latest()->paginate(20);

        $statuses = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled'
        ];

        $tiers = ['gold' => 'Gold', 'platinum' => 'Platinum', 'diamond' => 'Diamond'];

        return view('admin.referrals', compact('referrals', 'statuses', 'tiers'));
    }

    /**
     * Update referral status.
     */
    public function updateReferralStatus(Request $request, Referral $referral)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,paid,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldStatus = $referral->status;

        switch ($request->status) {
            case 'approved':
                $referral->approve();
                break;
            case 'paid':
                $referral->markAsPaid();
                break;
            case 'cancelled':
                $referral->cancel($request->notes);
                break;
            default:
                $referral->update(['status' => $request->status]);
        }

        return back()->with('success', "Referral status updated from {$oldStatus} to {$request->status}.");
    }

    /**
     * Process bulk payout for approved referrals.
     */
    public function bulkPayout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_ids' => 'required|array',
            'referral_ids.*' => 'exists:referrals,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $referrals = Referral::whereIn('id', $request->referral_ids)
                            ->where('status', 'approved')
                            ->get();

        if ($referrals->isEmpty()) {
            return back()->with('error', 'No approved referrals found for payout.');
        }

        $totalAmount = $referrals->sum('commission_amount');
        $processedCount = 0;

        DB::transaction(function () use ($referrals, &$processedCount) {
            foreach ($referrals as $referral) {
                $referral->markAsPaid();
                
                // Create commission payout transaction
                Transaction::createCommissionPayout(
                    $referral->referrer,
                    $referral->commission_amount,
                    [$referral->id]
                );
                
                $processedCount++;
            }
        });

        return back()->with('success', "Successfully processed {$processedCount} referral payouts totaling ₱" . number_format($totalAmount, 2));
    }

    /**
     * Display transactions management page.
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with('user');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $transactions = $query->latest()->paginate(20);

        $types = [
            'membership_upgrade' => 'Membership Upgrade',
            'commission_payout' => 'Commission Payout'
        ];

        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled'
        ];

        return view('admin.transactions', compact('transactions', 'types', 'statuses'));
    }

    /**
     * Show specific transaction details.
     */
    public function showTransaction(Transaction $transaction)
    {
        $transaction->load('user');
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Display reports page.
     */
    public function reports()
    {
        $dateRange = request('date_range', '30'); // Default to last 30 days
        $startDate = now()->subDays($dateRange);

        $reports = [
            'revenue' => [
                'total' => Transaction::where('payment_status', 'completed')
                                    ->where('type', 'membership_upgrade')
                                    ->where('created_at', '>=', $startDate)
                                    ->sum('amount'),
                'by_tier' => Transaction::where('payment_status', 'completed')
                                      ->where('type', 'membership_upgrade')
                                      ->where('created_at', '>=', $startDate)
                                      ->join('memberships', 'transactions.id', '=', 'memberships.transaction_id')
                                      ->selectRaw('memberships.tier, SUM(transactions.amount) as total')
                                      ->groupBy('memberships.tier')
                                      ->get(),
            ],
            'memberships' => [
                'new_signups' => User::where('created_at', '>=', $startDate)->count(),
                'upgrades' => Membership::where('payment_status', 'completed')
                                       ->where('created_at', '>=', $startDate)
                                       ->count(),
                'by_tier' => Membership::where('payment_status', 'completed')
                                      ->where('created_at', '>=', $startDate)
                                      ->selectRaw('tier, COUNT(*) as count')
                                      ->groupBy('tier')
                                      ->get(),
            ],
            'referrals' => [
                'total' => Referral::where('created_at', '>=', $startDate)->count(),
                'commissions_paid' => Referral::where('status', 'paid')
                                             ->where('paid_at', '>=', $startDate)
                                             ->sum('commission_amount'),
                'top_referrers' => Referral::where('created_at', '>=', $startDate)
                                          ->selectRaw('referrer_id, COUNT(*) as referral_count, SUM(commission_amount) as total_commission')
                                          ->groupBy('referrer_id')
                                          ->orderBy('referral_count', 'desc')
                                          ->with('referrer')
                                          ->take(10)
                                          ->get(),
            ],
        ];

        return view('admin.reports', compact('reports', 'dateRange'));
    }

    /**
     * Export reports.
     */
    public function exportReports(Request $request)
    {
        // This would typically generate a CSV or Excel file
        // For now, we'll return a simple response
        return response()->json(['message' => 'Export functionality would be implemented here']);
    }

    /**
     * Display settings page.
     */
    public function settings()
    {
        $settings = [
            'membership_prices' => config('paymongo.membership_prices'),
            'commission_rates' => config('paymongo.commission_rates'),
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        // This would typically update configuration values
        // For now, we'll return a simple response
        return back()->with('success', 'Settings updated successfully.');
    }
}
