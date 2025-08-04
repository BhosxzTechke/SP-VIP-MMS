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
use Illuminate\Support\Facades\Http;



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
        // Create PaymentIntent
        $result = $this->paymentService->createPaymentIntent($user, $request->tier);
        if (!$result['success']) {
            return back()->with('error', 'Failed to create payment intent.');
        }

        // Create PaymentMethod
        $paymentMethodResult = $this->paymentService->createPaymentMethod($request->payment_method, $user);
        if (!$paymentMethodResult['success']) {
            return back()->with('error', 'Failed to create payment method.');
        }

        // Attach
        $attachResult = $this->paymentService->attachPaymentMethod(
            $result['payment_intent']['id'],
            $paymentMethodResult['payment_method']['id'],
            $request->payment_method // <-- NEW: Pass type
        );

        if (!$attachResult['success']) {
            return back()->with('error', 'Failed to attach payment method.');
        }

        $paymentIntent = $attachResult['payment_intent'];
        $status = $paymentIntent['attributes']['status'];
        $redirectUrl = $paymentIntent['attributes']['next_action']['redirect']['url'] ?? null;

        if ($redirectUrl) {
            return redirect($redirectUrl); // For gcash, grab_pay, etc.
        }

        // For card or non-redirect payments
        if (in_array($status, ['succeeded', 'processing', 'awaiting_payment_method'])) {
            return redirect()->route('payment.success')->with('status', "Payment {$status}.");
        }

        return back()->with('error', 'Unexpected payment status.');

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
                $sourceId = $request->query('id'); // This should be like "src_ABC123"

                if (!$sourceId) {
                    return redirect()->route('dashboard')->with('error', 'Invalid payment confirmation.');
                }

                // Now fetch source status from PayMongo
                $response = Http::withBasicAuth(config('paymongo.secret_key'), '')
                    ->get("https://api.paymongo.com/v1/sources/{$sourceId}");

                if ($response->failed()) {
                    return redirect()->route('dashboard')->with('error', 'Unable to verify payment status.');
                }

                $source = $response->json()['data'];
                $status = $source['attributes']['status'];

                if ($status !== 'paid') {
                    return redirect()->route('dashboard')->with('error', 'Payment not completed.');
                }

                // Optional: read membership tier from metadata
                $metadata = $source['attributes']['metadata'] ?? [];
                $tier = $metadata['membership_tier'] ?? null;

                $user = Auth::user();
                if ($tier) {
                    $user->membership_type = $tier;
                    $user->save();
                }

                session()->forget(['payment_intent_id', 'client_key', 'tier', 'payment_method', 'amount']);

                return view('payment.success', compact('user', 'tier'));
                
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
