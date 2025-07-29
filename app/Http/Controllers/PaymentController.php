<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Membership;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show the upgrade page.
     */
    public function showUpgrade()
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

    /**
     * Handle checkout process.
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tier' => 'required|in:gold,platinum,diamond',
            'payment_method' => 'required|in:card,gcash,grab_pay,paymaya',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        if ($user->isVip()) {
            return redirect()->route('dashboard')->with('error', 'You are already a VIP member.');
        }

        try {
            $result = $this->paymentService->createPaymentIntent($user, $request->tier);

            if (!$result['success']) {
                return back()->with('error', 'Failed to create payment. Please try again.');
            }

            // Store payment details in session for the checkout page
            session([
                'payment_intent_id' => $result['payment_intent']['id'],
                'client_key' => $result['client_key'],
                'tier' => $request->tier,
                'payment_method' => $request->payment_method,
                'amount' => config("paymongo.membership_prices.{$request->tier}") / 100,
            ]);

            return view('payment.checkout', [
                'paymentIntent' => $result['payment_intent'],
                'clientKey' => $result['client_key'],
                'tier' => $request->tier,
                'paymentMethod' => $request->payment_method,
                'amount' => config("paymongo.membership_prices.{$request->tier}") / 100,
                'formattedAmount' => '₱' . number_format(config("paymongo.membership_prices.{$request->tier}") / 100, 2),
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Checkout error', [
                'user_id' => $user->id,
                'tier' => $request->tier,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred during checkout. Please try again.');
        }
    }

    /**
     * Handle successful payment.
     */
    public function paymentSuccess(Request $request)
    {
        $user = Auth::user();
        $paymentIntentId = $request->get('payment_intent');

        if (!$paymentIntentId) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment confirmation.');
        }

        // Find the membership by PayMongo payment ID
        $membership = Membership::where('paymongo_payment_id', $paymentIntentId)
                                ->where('user_id', $user->id)
                                ->first();

        if (!$membership) {
            return redirect()->route('dashboard')->with('error', 'Payment record not found.');
        }

        // Clear payment session data
        session()->forget(['payment_intent_id', 'client_key', 'tier', 'payment_method', 'amount']);

        return view('payment.success', compact('user', 'membership'));
    }

    /**
     * Handle cancelled payment.
     */
    public function paymentCancel(Request $request)
    {
        $user = Auth::user();
        
        // Clear payment session data
        session()->forget(['payment_intent_id', 'client_key', 'tier', 'payment_method', 'amount']);

        return view('payment.cancel', compact('user'));
    }

    /**
     * Handle PayMongo webhook.
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('PayMongo-Signature');

            // Verify webhook signature
            if (!$this->paymentService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Invalid webhook signature', [
                    'signature' => $signature,
                    'payload_length' => strlen($payload),
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = json_decode($payload, true);

            if (!$data) {
                Log::error('Invalid webhook payload', ['payload' => $payload]);
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            $processed = $this->paymentService->processWebhook($data);

            if ($processed) {
                return response()->json(['status' => 'success']);
            }

            return response()->json(['error' => 'Processing failed'], 500);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
