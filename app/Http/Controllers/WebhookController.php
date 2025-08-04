<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Membership;

class WebhookController extends Controller
{
    //
        public function handle(Request $request)
    {
        // Log everything for debugging
        Log::info('PayMongo Webhook Received:', $request->all());

        $data = $request->input('data');
        $event = $data['attributes'] ?? [];

        // 1. Check if payment is successful
        if ($data['type'] === 'payment' && $event['status'] === 'paid') {

            $paymongoId = $data['id'];

            // 2. Get metadata if available (recommended when creating payment)
            $metadata = $event['metadata'] ?? [];
            $userId = $metadata['user_id'] ?? null;
            $tier = $metadata['tier'] ?? null;

            if (!$userId || !$tier) {
                Log::warning("Missing metadata: user_id or tier");
                return response()->json(['error' => 'Missing metadata'], 400);
            }

            $user = User::find($userId);
            if (!$user) {
                Log::warning("User not found for ID: $userId");
                return response()->json(['error' => 'User not found'], 404);
            }

            DB::beginTransaction();
            try {
                // 3. Create transaction
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'membership',
                    'amount' => config("paymongo.membership_prices.$tier"),
                    'currency' => 'PHP',
                    'payment_method' => 'paymongo',
                    'payment_metadata' => json_encode($event),
                    'external_payment_id' => $paymongoId,
                    'status' => 'paid',
                ]);

                // 4. Create membership
                Membership::create([
                    'user_id' => $user->id,
                    'tier' => $tier,
                    'amount' => config("paymongo.membership_prices.$tier"),
                    'payment_status' => 'paid',
                    'transaction_id' => $transaction->id,
                    'paymongo_payment_id' => $paymongoId,
                    'payment_details' => json_encode($event),
                    'activated_at' => now(),
                    'expires_at' => now()->addYear(),
                ]);

                // 5. Referral logic
                if ($user->referred_by) {
                    $referrer = User::find($user->referred_by);
                    $rate = ($referrer && $referrer->user_type === 'vip') ? 0.05 : 0.03;
                    $commission = config("paymongo.membership_prices.$tier") * $rate;

                    DB::table('referrals')->insert([
                        'referrer_id' => $referrer->id,
                        'referred_id' => $user->id,
                        'commission_rate' => $rate * 100,
                        'commission_amount' => $commission,
                        'status' => 'earned',
                        'trigger_event' => 'membership_paid',
                        'approved_at' => now(),
                        'paid_at' => null,
                        'notes' => "Referral commission from {$user->name}'s $tier membership",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();
                return response()->json(['status' => 'success']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Webhook DB error: ' . $e->getMessage());
                return response()->json(['error' => 'DB error'], 500);
            }
        }

        return response()->json(['status' => 'ignored']);
    }
}
