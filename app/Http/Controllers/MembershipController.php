<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Models\Membership;
use App\Models\Transaction; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class MembershipController extends Controller
{
    //

       public function showForm()
    {
        $user = Auth::user();
        
        if ($user->isVip()) {
            return redirect()->route('dashboard')->with('info', 'You are already a VIP member.');
        }

        $membershipPricing = [
            'gold' => [
                'price' => config('paymongo.membership_prices.gold') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.gold') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support'
                ]
            ],
            'platinum' => [
                'price' => config('paymongo.membership_prices.platinum') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.platinum') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support',
                    'Exclusive Platinum member events'
                ]
            ],
            'diamond' => [
                'price' => config('paymongo.membership_prices.diamond') / 100,
                'formatted_price' => '₱' . number_format(config('paymongo.membership_prices.diamond') / 100, 0),
                'commission_rates' => config('paymongo.commission_rates.vip'),
                'benefits' => [
                    '5% commission on Gold referrals',
                    '8% commission on Platinum referrals',
                    '12% commission on Diamond referrals',
                    'Can refer all membership tiers',
                    'Priority customer support',
                    'Exclusive Diamond member events',
                    'Personal account manager',
                    'Highest commission rates'
                ]
            ],
        ];

        return view('payment.upgrade', compact('user', 'membershipPricing'));
    }


public function subscribe(Request $request)
{
    $request->validate([
        'tier' => 'required|in:gold,platinum,diamond',
        'payment_method' => 'required|in:card,gcash,grab_pay,paymaya'
    ]);

    $membership_prices = config('paymongo.membership_prices');

    $amount = $membership_prices[$request->tier];

        $localTunnel = 'https://careful-kingfishers-stand.loca.lt'; // ← your unique tunnel link

    $payload = [
        'data' => [
            'attributes' => [
                'amount' => $amount,
                'description' => ucfirst($request->tier) . ' Membership',
                'remarks' => 'Membership Subscription',
                'payment_method_types' => [$request->payment_method],
                'redirect' => [
                    'success' => $localTunnel . '/membership/success?tier=' . $request->tier,
                    'failed' => $localTunnel . '/membership/failed',
                ],
                'metadata' => [
                    'user_id' => Auth::id(),
                    'tier' => $request->tier,
                ],
            ]
        ]
    ];

    $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
        ->withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])
        ->post('https://api.paymongo.com/v1/links', $payload);

    $checkoutUrl = $response['data']['attributes']['checkout_url'];

    session(['selected_tier' => $request->tier]);

    return redirect($checkoutUrl);
}




public function success(Request $request)
{
    $tier = $request->get('tier');

    if (!$tier) {
        return redirect()->route('membership.form')->with('error', 'No membership tier found.');
    }

    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please log in to complete the membership.');
    }

    $user = Auth::user();

    DB::beginTransaction();

    try {
        // Save the transaction first
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'membership',
            'amount' => config("paymongo.membership_prices.$tier"),
            'currency' => 'PHP',
            'payment_method' => 'manual',
            'payment_metadata' => null,
            'external_payment_id' => 'manual',
            'status' => 'paid',
            'created_at' => now(),
        ]);

        // Create the membership record
        Membership::create([
            'user_id' => $user->id,
            'tier' => $tier,
            'amount' => config("paymongo.membership_prices.$tier"),
            'payment_status' => 'paid',
            'transaction_id' => $transaction->id,
            'paymongo_payment_id' => 'manual',
            'payment_details' => null,
            'activated_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        
        // Check if user was referred by someone
        if ($user->referred_by) {
            $referrer = \App\Models\User::find($user->referred_by);

            // Set base commission rate (e.g., 3% if free, 5% if VIP)
            $referrerRate = ($referrer->user_type === 'vip') ? 0.05 : 0.03;
            $commission = config("paymongo.membership_prices.$tier") * $referrerRate;

            // Save referral record
            DB::table('referrals')->insert([
                'referrer_id' => $referrer->id,
                'referred_id' => $user->id,
                'commission_rate' => $referrerRate * 100, // store as 3 or 5
                'commission_amount' => $commission,
                'status' => 'earned', // or 'pending'
                'trigger_event' => 'membership_paid',
                'approved_at' => now(),
                'paid_at' => null,
                'notes' => "Referral commission from {$user->name}'s $tier membership",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        DB::commit();
        return view('payment.successful');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('membership.form')->with('error', 'Payment failed: ' . $e->getMessage());
    }

}

    public function failed()
    {
        return view('payment.failed');
    }


}
