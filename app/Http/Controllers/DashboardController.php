<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Referral;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Redirect to appropriate dashboard based on membership type
        if ($user->isVip()) {
            return $this->vipDashboard();
        }

        return $this->freeDashboard();
    }

    /**
     * Display the free user dashboard.
     */
    public function freeDashboard()
    {
        $user = Auth::user();

        if ($user->isVip()) {
            return redirect()->route('dashboard.vip');
        }

        $stats = [
            'total_referrals' => $user->referralsSent()->count(),
            'pending_referrals' => $user->referralsSent()->where('status', 'pending')->count(),
            'approved_referrals' => $user->referralsSent()->where('status', 'approved')->count(),
            'total_commission' => $user->total_commission,
            'pending_commission' => $user->pending_commission,
        ];

        $recentReferrals = $user->referralsSent()
                               ->with('referred')
                               ->latest()
                               ->take(5)
                               ->get();

        $membershipPricing = [
            'gold' => [
                'price' => config('paymongo.membership_prices.gold') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.gold') / 100, 0),
            ],
            'platinum' => [
                'price' => config('paymongo.membership_prices.platinum') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.platinum') / 100, 0),
            ],
            'diamond' => [
                'price' => config('paymongo.membership_prices.diamond') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.diamond') / 100, 0),
            ],
        ];

        return view('dashboard.free', compact('user', 'stats', 'recentReferrals', 'membershipPricing'));
    }

    /**
     * Display the VIP user dashboard.
     */
    public function vipDashboard()
    {
        $user = Auth::user();

        if (!$user->isVip()) {
            return redirect()->route('dashboard.free');
        }

        $stats = [
            'total_referrals' => $user->referralsSent()->count(),
            'gold_referrals' => $user->referralsSent()->where('membership_tier_referred', 'gold')->count(),
            'platinum_referrals' => $user->referralsSent()->where('membership_tier_referred', 'platinum')->count(),
            'diamond_referrals' => $user->referralsSent()->where('membership_tier_referred', 'diamond')->count(),
            'total_commission' => $user->total_commission,
            'pending_commission' => $user->pending_commission,
            'this_month_commission' => $user->referralsSent()
                                           ->where('status', 'paid')
                                           ->whereMonth('paid_at', now()->month)
                                           ->sum('commission_amount'),
        ];

        $recentReferrals = $user->referralsSent()
                               ->with('referred')
                               ->latest()
                               ->take(10)
                               ->get();

        $monthlyCommissions = $user->referralsSent()
                                  ->where('status', 'paid')
                                  ->selectRaw('MONTH(paid_at) as month, YEAR(paid_at) as year, SUM(commission_amount) as total')
                                  ->groupBy('year', 'month')
                                  ->orderBy('year', 'desc')
                                  ->orderBy('month', 'desc')
                                  ->take(12)
                                  ->get();

        return view('dashboard.vip', compact('user', 'stats', 'recentReferrals', 'monthlyCommissions'));
    }

    /**
     * Display user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Display referrals page.
     */
    public function referrals()
    {
        $user = Auth::user();
        
        $referrals = $user->referralsSent()
                          ->with('referred')
                          ->latest()
                          ->paginate(20);

        $stats = [
            'total_referrals' => $user->referralsSent()->count(),
            'pending_referrals' => $user->referralsSent()->where('status', 'pending')->count(),
            'approved_referrals' => $user->referralsSent()->where('status', 'approved')->count(),
            'paid_referrals' => $user->referralsSent()->where('status', 'paid')->count(),
        ];

        return view('dashboard.referrals', compact('user', 'referrals', 'stats'));
    }

    /**
     * Display referral history.
     */
    public function referralHistory()
    {
        $user = Auth::user();
        
        $referrals = $user->referralsSent()
                          ->with('referred')
                          ->where('status', 'paid')
                          ->latest('paid_at')
                          ->paginate(20);

        $totalCommission = $user->referralsSent()
                               ->where('status', 'paid')
                               ->sum('commission_amount');

        return view('dashboard.referral-history', compact('user', 'referrals', 'totalCommission'));
    }

    /**
     * Display transactions page.
     */
    public function transactions()
    {
        $user = Auth::user();
        
        $transactions = $user->transactions()
                            ->latest()
                            ->paginate(20);

        $stats = [
            'total_transactions' => $user->transactions()->count(),
            'completed_transactions' => $user->transactions()->where('payment_status', 'completed')->count(),
            'pending_transactions' => $user->transactions()->where('payment_status', 'pending')->count(),
            'failed_transactions' => $user->transactions()->where('payment_status', 'failed')->count(),
        ];

        return view('dashboard.transactions', compact('user', 'transactions', 'stats'));
    }
}
